<?php

declare(strict_types=1);

namespace Src\Domain\Dispatch\Exceptions;

use RuntimeException;

class NoDriverAvailableException extends RuntimeException
{
    public static function forOrder(int $orderId): self
    {
        return new self("No available driver could be assigned to order [{$orderId}].");
    }
}
