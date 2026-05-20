<?php

use App\Providers\AppServiceProvider;
use Src\Domain\Dispatch\Providers\DispatchServiceProvider;
use Src\Domain\Driver\Providers\DriverServiceProvider;
use Src\Domain\Order\Providers\OrderServiceProvider;
use Src\Presentation\Api\Providers\ApiRouteServiceProvider;

return [
    AppServiceProvider::class,
    OrderServiceProvider::class,
    DriverServiceProvider::class,
    DispatchServiceProvider::class,
    ApiRouteServiceProvider::class,
];
