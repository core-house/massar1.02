<?php

use Illuminate\Support\Facades\Route;
use Modules\Shipping\Http\Controllers\ShipmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('shipments', ShipmentController::class)->names('shipments');
});
