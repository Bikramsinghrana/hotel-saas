<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\SidebarController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\HotelMasterController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\RoomController;

// Admin Domain Specific Routes (Prefix: admin, Name: admin.)

Route::resource('roles', RoleController::class);
Route::resource('hotels', HotelController::class);
Route::post('hotels/bulk-delete', [HotelController::class, 'bulkDelete'])->name('hotels.bulk-delete');
Route::patch('hotels/{hotel}/status', [HotelController::class, 'updateStatus'])->name('hotels.update-status');

Route::resource('room-types', RoomTypeController::class);
Route::resource('rooms', RoomController::class);
Route::post('rooms/bulk-delete', [RoomController::class, 'bulkDelete'])->name('rooms.bulk-delete');
Route::patch('rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('rooms.update-status');

// Consolidated Master Data (Amenities, Guest Services, Room Facilities)
Route::resource('masters', HotelMasterController::class);

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

// Hotel Wizard routes
Route::controller(App\Http\Controllers\Admin\HotelWizardController::class)->prefix('hotels/wizard')->name('hotels.wizard.')->group(function () {
    Route::get('create', 'create')->name('create');
    Route::get('{id}/edit', 'edit')->name('edit');
    Route::post('store', 'store')->name('store');
    Route::post('{id}/media', 'uploadMedia')->name('media.upload');
    Route::delete('{id}/media/{mediaId}', 'deleteMedia')->name('media.delete');
    Route::post('{id}/rooms', 'storeRoom')->name('rooms.store');
});
