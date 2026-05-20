<?php

declare(strict_types=1);

namespace Src\Domain\Order\Providers;

use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * Contract => implementation bindings for the Order domain.
     * Consumers depend on the Contract only; the concrete service stays internal.
     */
    public array $bindings = [
        // \Src\Domain\Order\Contracts\OrderContract::class => \Src\Domain\Order\Services\OrderService::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
