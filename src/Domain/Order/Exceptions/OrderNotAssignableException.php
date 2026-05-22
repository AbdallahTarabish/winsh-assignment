<?php

declare(strict_types=1);

namespace Src\Domain\Order\Exceptions;

use RuntimeException;
use Src\Domain\Order\Enums\OrderStatus;

class OrderNotAssignableException extends RuntimeException
{
    public function __construct(
        public readonly int $orderId,
        public readonly OrderStatus $currentStatus,
    ) {
        parent::__construct(
            "Order [{$orderId}] cannot be assigned from status [{$currentStatus->value}]."
        );
    }
}
