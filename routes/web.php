<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\User\ProfileController;

Route::get('/', [PageController::class, 'index']);
Route::get('/rooms/index', [App\Http\Controllers\RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{id}/checkout', [App\Http\Controllers\RoomController::class, 'checkout'])->name('rooms.checkout');
Route::post('/rooms/{id}/book', [App\Http\Controllers\RoomController::class, 'book'])->name('rooms.book');

// Authentication routes
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('login');
    Route::post('login', 'login');
    Route::get('seller/login', 'showSellerLoginForm')->name('seller.login');
    Route::post('seller/login', 'sellerLogin');
    Route::post('logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('register', 'showRegistrationForm')->name('register');
    Route::post('register', 'register');
});

Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('password/reset', 'showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'sendResetLinkEmail')->name('password.email');
});

Route::controller(ResetPasswordController::class)->group(function () {
    Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    Route::post('password/reset', 'reset')->name('password.update');
});

// User profile routes & Customer Dashboard
Route::middleware('auth')->group(function () {
    Route::get('customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'show')->name('show');
        Route::get('edit', 'edit')->name('edit');
        Route::post('/', 'update')->name('update');
    });
});


