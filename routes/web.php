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
Route::controller(App\Http\Controllers\RoomController::class)->group(function () {
    Route::get('/rooms/index', 'index')->name('rooms.index');
    Route::get('/rooms/{id}/checkout', 'checkout')->name('rooms.checkout');
    Route::post('/rooms/checkout-init', 'checkoutInit')->name('rooms.checkout.init');
    Route::post('/rooms/{id}/book', 'book')->name('rooms.book');
    Route::post('/rooms/{id}/book-ajax', 'bookAjax')->name('rooms.book.ajax');
    Route::get('/rooms/{order}/complete', 'bookingComplete')->name('rooms.booking.complete');
    Route::get('/api/coupons/validate', 'validateCoupon')->name('api.coupons.validate');
});

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
    use App\Http\Controllers\PaymentController;
    use App\Http\Controllers\StripeWebhookController;

    Route::post('payments/checkout/{orderId}', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::get('payments/success', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('payments/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
    Route::get('payments/invoice/{invoiceId}', [PaymentController::class, 'downloadInvoice'])->name('payments.invoice.download');
    Route::get('payments/status', [PaymentController::class, 'status'])->name('payments.status');
    Route::get('payments/order/{orderId}/status', [PaymentController::class, 'orderStatus'])->name('payments.order.status');

    // Stripe webhook endpoint (public)
    Route::post('stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');


