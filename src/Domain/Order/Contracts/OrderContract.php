<?php

declare(strict_types=1);

namespace Src\Domain\Order\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Src\Domain\Order\DataTransferObjects\OrderSnapshot;
use Src\Domain\Order\Enums\OrderStatus;

/**
 * The gateway into the Order domain. Everything other domains are allowed to
 * know about orders is expressed here; the Eloquent entity stays internal.
 */
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
     * MUST be called inside a database transaction.
     */
    public function lockForAssignment(int $orderId): ?OrderSnapshot;

    /**
     * Move an order to "assigned" and bind it to the given driver.
     */
    public function assignToDriver(int $orderId, int $driverId): void;

    /**
     * Of the given driver ids, those that currently hold an active
     * (assigned/in-progress) order. Scoped to the candidate set — never the
     * whole fleet.
     *
     * @param  array<int, int>  $driverIds
     * @return array<int, int>
     */
    public function busyAmong(array $driverIds): array;

    /**
     * Whether the given driver currently holds an active order.
     */
    public function driverHasActiveOrder(int $driverId): bool;
}
