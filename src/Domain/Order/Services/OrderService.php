<?php

declare(strict_types=1);

namespace Src\Domain\Order\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Src\Domain\Order\Contracts\OrderContract;
use Src\Domain\Order\DataTransferObjects\OrderSnapshot;
use Src\Domain\Order\Enums\OrderStatus;
use Src\Domain\Order\Models\Entities\Order;
use Src\Domain\Order\Models\Scopes\ActiveScope;

class OrderService implements OrderContract
{
    public function paginate(?OrderStatus $status, int $perPage): LengthAwarePaginator
    {
        return Order::query()
            ->when($status, fn ($q) => $q->where('status', $status->value))
            ->latest()
            ->paginate($perPage);
    }

    public function paginateForDriver(int $driverId, ?OrderStatus $status, int $perPage): LengthAwarePaginator
    {
        return Order::query()
            ->where('driver_id', $driverId)
            ->when($status, fn ($q) => $q->where('status', $status->value))
            ->latest()
            ->paginate($perPage);
    }

    public function lockForAssignment(int $orderId): ?OrderSnapshot
    {
        $order = Order::query()->whereKey($orderId)->lockForUpdate()->first();

        if ($order === null) {
            return null;
        }

        return new OrderSnapshot(
            id: $order->id,
            status: $order->status,
            driverId: $order->driver_id,
            pickupLat: $order->pickup_lat,
            pickupLng: $order->pickup_lng,
        );
    }

    public function assignToDriver(int $orderId, int $driverId): void
    {
        Order::query()->whereKey($orderId)->update([
            'driver_id' => $driverId,
            'status' => OrderStatus::Assigned->value,
            'assigned_at' => now(),
        ]);
    }

    public function busyAmong(array $driverIds): array
    {
        if ($driverIds === []) {
            return [];
        }

        return Order::query()
            ->whereIn('driver_id', $driverIds)
            ->tap(new ActiveScope())
            ->distinct()
            ->pluck('driver_id')
            ->all();
    }

    public function driverHasActiveOrder(int $driverId): bool
    {
        return Order::query()
            ->where('driver_id', $driverId)
            ->tap(new ActiveScope())
            ->exists();
    }
}
