<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\TenantMiddleware;
use App\Http\Middleware\HandleDatabaseConnection;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Super Admin Platform Routes
            Route::middleware(['web', 'auth'])->prefix('superadmin')->name('superadmin.')->group(function () {
                if (file_exists(base_path('routes/superadmin.php'))) {
                    require base_path('routes/superadmin.php');
                }
            });

            // Tenant Theme Admin Routes
            Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
                if (file_exists(base_path('routes/hotel.php'))) {
                    require base_path('routes/hotel.php');
                }
                if (file_exists(base_path('routes/resto.php'))) {
                    require base_path('routes/resto.php');
                }
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            TenantMiddleware::class,
            \App\Http\Middleware\ThemeMiddleware::class,
            HandleDatabaseConnection::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (Throwable $e, $request) {

            if (
                $e instanceof PDOException ||
                $e instanceof QueryException ||
                str_contains($e->getMessage(), 'SQLSTATE[HY000] [2002]') ||
                str_contains($e->getMessage(), 'Connection refused')
            ) {

                Log::error('Database Connection Failed', [
                    'message' => $e->getMessage(),
                    'url' => $request->fullUrl(),
                ]);

                return response()->view(
                    'errors.database',
                    [
                        'message' => 'Database server is currently unavailable. Please try again later.'
                    ],
                    503
                );
            }
        });
    })->create();
