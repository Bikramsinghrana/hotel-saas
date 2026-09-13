<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use App\Repositories\PaymentRepositoryInterface;
use App\Repositories\PaymentRepository;
use App\Services\PaymentServiceInterface;
use App\Services\PaymentService;
use App\Services\TenantAccessService;
use App\Enums\RoleEnum;

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

        // Register TenantAccessService as singleton
        $this->app->singleton(TenantAccessService::class, function ($app) {
            return new TenantAccessService();
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
        // Super Admin automatically bypasses all permission and gate checks across the application
        Gate::before(function ($user, $ability) {
            if ($user && $user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
                return true;
            }
            return null;
        });

        // Custom Blade Directives for feature gating
        Blade::if('feature', function (string $featureKey) {
            if (is_super_admin()) {
                return true;
            }
            $tenant = tenant();
            return app(TenantAccessService::class)->canAccessFeature($tenant, $featureKey);
        });

        Blade::if('theme', function (string $themeKey) {
            $tenant = tenant();
            return $tenant && $tenant->theme && $tenant->theme->key === $themeKey;
        });

        Blade::if('subtheme', function (string $subThemeKey) {
            $tenant = tenant();
            return $tenant && $tenant->subTheme && $tenant->subTheme->key === $subThemeKey;
        });

        Blade::if('superadmin', function () {
            return is_super_admin();
        });

        Blade::if('canAction', function (string $permission) {
            return can_action($permission);
        });
    }
}
