<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\SidebarController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\HotelWizardController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\RoomController;

// Admin Domain Specific Routes, set default (Prefix: admin, Name: admin.) ++++++++++++++++++++++++++++++++++++

Route::resource('roles', RoleController::class);
Route::resource('hotels', HotelController::class);
Route::post('hotels/bulk-delete', [HotelController::class, 'bulkDelete'])->name('hotels.bulk-delete');
Route::patch('hotels/{hotel}/status', [HotelController::class, 'updateStatus'])->name('hotels.update-status');

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
Route::controller(HotelWizardController::class)->prefix('hotels/wizard')->name('hotels.wizard.')->group(function () {
    Route::get('create', 'create')->name('create');
    Route::get('{id}/edit', 'edit')->name('edit');
    Route::post('store', 'store')->name('store');
    Route::post('{id}/media', 'uploadMedia')->name('media.upload');
    Route::delete('{id}/media/{mediaId}', 'deleteMedia')->name('media.delete');
    Route::post('{id}/rooms', 'storeRoom')->name('rooms.store');
});
// Room & Wizard routes
Route::resource('room-types', RoomTypeController::class);
Route::resource('rooms', RoomController::class);
Route::post('rooms/bulk-delete', [RoomController::class, 'bulkDelete'])->name('rooms.bulk-delete');
Route::patch('rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('rooms.update-status');

Route::controller(App\Http\Controllers\Admin\RoomWizardController::class)->prefix('rooms/wizard')->name('rooms.wizard.')->group(function () {
    Route::get('create', 'create')->name('create');
    Route::get('{id}/edit', 'edit')->name('edit');
    Route::post('store', 'store')->name('store');
    Route::post('{id}/media', 'uploadMedia')->name('media.upload');
    Route::delete('{id}/media/{mediaId}', 'deleteMedia')->name('media.delete');
});

// Term management (Consolidated Master Data)
Route::controller(App\Http\Controllers\Admin\TermController::class)->prefix('terms')->name('terms.')->group(function () {
    Route::post('/', 'store')->name('store');
    Route::get('/{type}', 'index')->name('index');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
});

// Coupon & Offer Management
Route::resource('coupons', App\Http\Controllers\Admin\CouponController::class);

// Admin payments
Route::get('payments', [App\Http\Controllers\Admin\PaymentAdminController::class, 'index'])->name('payments.index');
Route::get('payments/{id}', [App\Http\Controllers\Admin\PaymentAdminController::class, 'show'])->name('payments.show');
Route::post('payments/{id}/status', [App\Http\Controllers\Admin\PaymentAdminController::class, 'updateStatus'])->name('payments.update_status');
Route::get('payments/invoice/{invoiceId}/download', [App\Http\Controllers\Admin\PaymentAdminController::class, 'downloadInvoice'])->name('payments.invoice.download');

// Admin bookings
Route::resource('bookings', App\Http\Controllers\Admin\BookingController::class);
Route::post('bookings/bulk-delete', [App\Http\Controllers\Admin\BookingController::class, 'bulkDelete'])->name('bookings.bulk-delete');
Route::post('bookings/import', [App\Http\Controllers\Admin\BookingController::class, 'importCsv'])->name('bookings.import');
Route::post('bookings/{id}/mark-paid', [App\Http\Controllers\Admin\BookingController::class, 'markPaid'])->name('bookings.mark-paid');
Route::post('bookings/{id}/resend-email', [App\Http\Controllers\Admin\BookingController::class, 'resendEmail'])->name('bookings.resend-email');

