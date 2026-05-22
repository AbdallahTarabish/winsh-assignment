<?php

declare(strict_types=1);

namespace Src\Domain\Order\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Src\Domain\Order\Enums\OrderStatus;


class ActiveScope
{
    public function __invoke(Builder $query): void
    {
        $query->whereIn('status', array_map(
            fn (OrderStatus $status): string => $status->value,
            OrderStatus::active(),
        ));
    }
}
