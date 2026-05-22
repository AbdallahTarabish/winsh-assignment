<?php

declare(strict_types=1);

namespace Src\Domain\Driver\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Src\Domain\Driver\Enums\DriverStatus;

/**
 * Constrains a driver query to online drivers
 */
class OnlineScope
{
    public function __invoke(Builder $query): void
    {
        $query->where('status', DriverStatus::Online->value);
    }
}
