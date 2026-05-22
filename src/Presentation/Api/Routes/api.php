<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Presentation\Api\Controllers\DriverOrderController;
use Src\Presentation\Api\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| WINCH API routes
|--------------------------------------------------------------------------
*/

Route::get('/ping', fn () => response()->json([
    'ok' => true,
    'service' => 'winch-api',
]));

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders/{order}/assign', [OrderController::class, 'assign'])->whereNumber('order');
    Route::get('/drivers/{driver}/orders', [DriverOrderController::class, 'index'])->whereNumber('driver');
});
