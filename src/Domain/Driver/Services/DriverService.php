<?php

declare(strict_types=1);

namespace Src\Domain\Driver\Services;

use Src\Domain\Driver\Contracts\DriverContract;
use Src\Domain\Driver\DataTransferObjects\AvailableDriver;
use Src\Domain\Driver\Enums\DriverStatus;
use Src\Domain\Driver\Models\Entities\Driver;
use Src\Domain\Driver\Models\Scopes\OnlineScope;

class DriverService implements DriverContract
{
    // A geography point literal, parameterised as (lng, lat) for ST_MakePoint.
    private const POINT = 'ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography';

    public function findById(int $driverId): ?AvailableDriver
    {
        $driver = Driver::query()->find($driverId);

        return $driver ? new AvailableDriver($driver->id, $driver->name) : null;
    }

    // PostGIS KNN: the GiST index on `location` drives the ORDER BY through the `<->` operator
    public function nearestOnlineWithin(float $lat, float $lng, int $limit, array $excludeDriverIds = []): array
    {
        $rows = Driver::query()
            ->select('id', 'name')
            ->selectRaw('ST_Distance(location, '.self::POINT.') / 1000 AS distance_km', [$lng, $lat])
            ->tap(new OnlineScope())
            ->whereNotNull('location')
            ->when($excludeDriverIds !== [], fn ($q) => $q->whereNotIn('id', $excludeDriverIds))
            ->orderByRaw('location <-> '.self::POINT, [$lng, $lat])
            ->limit($limit)
            ->get();

        return $rows
            ->map(fn (Driver $driver) => new AvailableDriver(
                id: $driver->id,
                name: $driver->name,
                distanceKm: round((float) $driver->distance_km, 3),
            ))
            ->all();
    }

    public function lockOnlineById(int $driverId): ?AvailableDriver
    {
        $driver = Driver::query()->whereKey($driverId)->lockForUpdate()->first();

        if ($driver === null || $driver->status !== DriverStatus::Online) {
            return null;
        }

        return new AvailableDriver($driver->id, $driver->name);
    }
}
