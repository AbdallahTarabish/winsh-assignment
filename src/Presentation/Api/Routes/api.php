<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WINCH API routes (Presentation\Api bounded context)
|--------------------------------------------------------------------------
| Loaded by ApiRouteServiceProvider under the "api" middleware group and
| the "/api" prefix. Domain endpoints are added per feature.
*/

Route::get('/ping', fn () => response()->json([
    'ok' => true,
    'service' => 'winch-api',
]));

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
});
