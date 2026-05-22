<?php

declare(strict_types=1);

namespace Src\Domain\Driver\Contracts;

use Src\Domain\Driver\DataTransferObjects\AvailableDriver;

/**
 * The gateway into the Driver domain.
 */
interface DriverContract
{
    public function findById(int $driverId): ?AvailableDriver;

    /**
     * The nearest online drivers to a point, distance-sorted, capped at $limit.
     *
     * @param  array<int, int>  $excludeDriverIds
     * @return array<int, AvailableDriver>
     */
    public function nearestOnlineWithin(float $lat, float $lng, int $limit, array $excludeDriverIds = []): array;

    /**
     * Lock the driver row FOR UPDATE and return it only if still online.
     */
    public function lockOnlineById(int $driverId): ?AvailableDriver;
}
