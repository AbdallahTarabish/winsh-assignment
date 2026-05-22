<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Src\Domain\Dispatch\Exceptions\NoDriverAvailableException;
use Src\Domain\Order\Exceptions\OrderNotAssignableException;
use Src\Domain\Order\Exceptions\OrderNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Translate domain exceptions into HTTP responses so the domain layer
        // stays free of any HTTP/Presentation concerns.
        $exceptions->render(fn (OrderNotFoundException $e, Request $request) => $request->expectsJson()
            ? response()->json(['message' => $e->getMessage()], 404)
            : null);

        $exceptions->render(fn (NoDriverAvailableException $e, Request $request) => $request->expectsJson()
            ? response()->json(['message' => $e->getMessage()], 422)
            : null);

        $exceptions->render(fn (OrderNotAssignableException $e, Request $request) => $request->expectsJson()
            ? response()->json(['message' => $e->getMessage()], 409)
            : null);
    })->create();
