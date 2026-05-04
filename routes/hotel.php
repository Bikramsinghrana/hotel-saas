<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\SidebarController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\RoleController;

// Admin Domain Specific Routes (Prefix: admin, Name: admin.)

Route::resource('roles', RoleController::class);

// Navigation Management
Route::controller(NavigationController::class)->group(function () {
    Route::post('navigations/{navigation}/toggle-active', 'toggleActive')->name('navigations.toggle-active');
});
Route::resource('navigations', NavigationController::class);

// Blog Management
Route::controller(BlogController::class)->group(function () {
    Route::post('blogs/{blog}/toggle-active', 'toggleActive')->name('blogs.toggle-active');
});
Route::resource('blogs', BlogController::class);

// Sidebar Management
Route::controller(SidebarController::class)->group(function () {
    Route::post('sidebars/{sidebar}/toggle-active', 'toggleActive')->name('sidebars.toggle-active');
});
Route::resource('sidebars', SidebarController::class);

// Settings & Themes
Route::controller(SettingController::class)->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('theme/main/activate', 'activateMainTheme')->name('theme.main.activate');
    Route::post('theme/activate', 'activateTheme')->name('theme.activate');
});

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('guests', [GuestController::class, 'index'])->name('guests.index');
