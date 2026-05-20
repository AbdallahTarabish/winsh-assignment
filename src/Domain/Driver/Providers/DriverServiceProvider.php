<?php

declare(strict_types=1);

namespace Src\Domain\Driver\Providers;

use Illuminate\Support\ServiceProvider;

class DriverServiceProvider extends ServiceProvider
{
    /**
     * Contract => implementation bindings for the Driver domain.
     * The Driver domain is upstream of Dispatch and exposes its capabilities
     * (find available, find nearest) only through its Contract.
     */
    public array $bindings = [
        // \Src\Domain\Driver\Contracts\DriverContract::class => \Src\Domain\Driver\Services\DriverService::class,
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
