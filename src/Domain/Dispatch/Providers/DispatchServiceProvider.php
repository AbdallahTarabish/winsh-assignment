<?php

declare(strict_types=1);

namespace Src\Domain\Dispatch\Providers;

use Illuminate\Support\ServiceProvider;

class DispatchServiceProvider extends ServiceProvider
{
    /**
     * Contract => implementation bindings for the Dispatch domain.
     * Dispatch orchestrates assignment and depends on Order/Driver via their Contracts only.
     */
    public array $bindings = [
        // \Src\Domain\Dispatch\Contracts\DispatchContract::class => \Src\Domain\Dispatch\Services\DispatchService::class,
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
