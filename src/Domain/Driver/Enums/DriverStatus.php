<?php

declare(strict_types=1);

namespace Src\Domain\Driver\Enums;

enum DriverStatus: string
{
    case Online = 'online';
    case Offline = 'offline';

    /**
     * Whether a driver in this status is eligible to receive assignments.
     * I think -> eligibility requires having no active order
     */
    public function isAssignable(): bool
    {
        return $this === self::Online;
    }
}
