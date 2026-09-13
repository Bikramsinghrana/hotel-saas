<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Themes\Resto\Admin\RestoController;

// Restaurant Domain Specific Routes (Prefix: admin/resto, Name: admin.resto.)
Route::prefix('resto')->name('resto.')->group(function () {
    // Menu & Catalog
    Route::get('menu', [RestoController::class, 'menuIndex'])->name('menu.index');
    Route::get('categories', [RestoController::class, 'categoriesIndex'])->name('categories.index');
    Route::get('addons', [RestoController::class, 'addonsIndex'])->name('addons.index');

    // Tables & Floor Plan
    Route::get('tables', [RestoController::class, 'tablesIndex'])->name('tables.index');
    Route::get('reservations', [RestoController::class, 'reservationsIndex'])->name('reservations.index');

    // POS Billing & Kitchen Display System
    Route::get('pos', [RestoController::class, 'posIndex'])->name('pos.index');
    Route::get('orders', [RestoController::class, 'ordersIndex'])->name('orders.index');
    Route::get('kds', [RestoController::class, 'kdsIndex'])->name('kds.index');
});
