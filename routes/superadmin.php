<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\ThemeController as SuperAdminThemeController;
use App\Http\Controllers\SuperAdmin\PlanController as SuperAdminPlanController;
use App\Http\Controllers\SuperAdmin\FeatureController as SuperAdminFeatureController;
use App\Http\Controllers\SuperAdmin\TenantController as SuperAdminTenantController;

// Platform Super Admin Routes (Prefix: superadmin, Name: superadmin.)
Route::get('dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

// Theme & Sub-theme Management
Route::resource('themes', SuperAdminThemeController::class);
Route::post('themes/{theme}/toggle-status', [SuperAdminThemeController::class, 'toggleStatus'])->name('themes.toggle-status');
Route::post('themes/{theme}/sub-themes', [SuperAdminThemeController::class, 'storeSubTheme'])->name('themes.sub-themes.store');
Route::put('sub-themes/{subTheme}', [SuperAdminThemeController::class, 'updateSubTheme'])->name('sub-themes.update');
Route::delete('sub-themes/{subTheme}', [SuperAdminThemeController::class, 'destroySubTheme'])->name('sub-themes.destroy');
Route::post('sub-themes/{subTheme}/toggle-status', [SuperAdminThemeController::class, 'toggleSubThemeStatus'])->name('sub-themes.toggle-status');

// Feature Catalog Management
Route::resource('features', SuperAdminFeatureController::class);

// Subscription Plans Builder
Route::resource('plans', SuperAdminPlanController::class);

// Merchant Tenants & Access Overrides
Route::resource('tenants', SuperAdminTenantController::class);
Route::post('tenants/{tenant}/feature-override', [SuperAdminTenantController::class, 'toggleFeatureOverride'])->name('tenants.feature-override');
Route::post('tenants/{tenant}/subtheme-override', [SuperAdminTenantController::class, 'toggleSubThemeOverride'])->name('tenants.subtheme-override');
