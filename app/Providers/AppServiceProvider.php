<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\PaymentRepositoryInterface;
use App\Repositories\PaymentRepository;
use App\Services\PaymentServiceInterface;
use App\Services\PaymentService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register PageModuleService as singleton
        $this->app->singleton('page-module-service', function ($app) {
            return new \App\Services\PageModuleService();
        });

        // Payment bindings
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
