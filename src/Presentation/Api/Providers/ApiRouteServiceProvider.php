<?php

declare(strict_types=1);

namespace Src\Presentation\Api\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ApiRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('src/Presentation/Api/Routes/api.php'));
    }
}
