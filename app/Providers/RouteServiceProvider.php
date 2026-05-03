<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This constant defines your application's "home" route.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Dynamic route path identifiers you can use across the app.
     */
    public const HOTEL_BACKEND = 'hotel-backend';
    public const EHOTEL_FRONTEND = 'ehotel-frontend';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // keep default behavior for now — extend if needed
    }
}
