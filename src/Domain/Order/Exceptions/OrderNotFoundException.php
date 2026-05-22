<?php

declare(strict_types=1);

namespace Src\Domain\Order\Exceptions;

use RuntimeException;

class OrderNotFoundException extends RuntimeException
{
    public static function withId(int $orderId): self
    {
        return new self("Order [{$orderId}] was not found.");
    }
}
