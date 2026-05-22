<?php

declare(strict_types=1);

namespace Src\Domain\Order\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Src\Domain\Order\DataTransferObjects\OrderSnapshot;
use Src\Domain\Order\Enums\OrderStatus;

interface OrderContract
{
    /**
     * Paginated orders, optionally filtered by status (newest first).
     */
    public function paginate(?OrderStatus $status, int $perPage): LengthAwarePaginator;

    /**
     * Paginated orders belonging to a driver, optionally filtered by status.
     */
    public function paginateForDriver(int $driverId, ?OrderStatus $status, int $perPage): LengthAwarePaginator;

    /**
     * Lock the order row FOR UPDATE and return a snapshot, or null if missing.
     */
    public function lockForAssignment(int $orderId): ?OrderSnapshot;

    /**
     * Move an order to "assigned" and bind it to the given driver.
     */
    public function assignToDriver(int $orderId, int $driverId): void;

    /**
     * Of the given driver ids, those that currently hold an active
     * (assigned/in-progress) order.
     * @param  array<int, int>  $driverIds
     * @return array<int, int>
     */
    public function busyAmong(array $driverIds): array;

    /**
     * Whether the given driver currently holds an active order.
     */
    public function driverHasActiveOrder(int $driverId): bool;
}
