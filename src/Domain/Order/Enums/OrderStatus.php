<?php

declare(strict_types=1);

namespace Src\Domain\Order\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * Statuses that mean a driver is currently committed to the order.
     * A driver holding an order in any of these cannot take a new one.
     *
     * @return array<int, self>
     */
    public static function active(): array
    {
        return [self::Assigned, self::InProgress];
    }

    public function isActive(): bool
    {
        return in_array($this, self::active(), true);
    }
}
