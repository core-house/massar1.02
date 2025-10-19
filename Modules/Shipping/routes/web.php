<?php

use Illuminate\Support\Facades\Route;
use Modules\Shipping\Http\Controllers\{
    ShippingCompanyController,
    ShipmentController,
    DriverController,
    OrderController,
    ShippingStatisticsController
};

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('companies', ShippingCompanyController::class)->names('companies');
    Route::resource('shipments', ShipmentController::class)->names('shipments');
    Route::resource('drivers', DriverController::class)->names('drivers');
    Route::resource('orders', OrderController::class)->names('orders');

    Route::get('dashboard/statistics', [ShippingStatisticsController::class, 'index'])
        ->name('shipping.dashboard.statistics');
});
