<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\{ManufacturingController, ManufacturingStageController, ManufacturingOrderController};

Route::middleware(['auth', 'verified', 'module.access:manufacturing'])->group(function () {

    // Specific routes must come BEFORE resource routes to avoid conflicts
    Route::get('/manufacturing/statistics', [ManufacturingController::class, 'manufacturingStatistics'])->name('manufacturing.statistics');
    
    Route::get(
        'manufacturing/stage-invoices-report',
        [ManufacturingController::class, 'stageInvoicesReport']
    )->name('manufacturing.stage-invoices-report');

    // Resource routes
    Route::resource('manufacturing', ManufacturingController::class)->names('manufacturing');
    Route::resource('manufacturing-stages', ManufacturingStageController::class)->names('manufacturing.stages');
    Route::resource('manufacturing-orders', ManufacturingOrderController::class)->names('manufacturing.orders');

    Route::patch(
        'manufacturing-stages/{manufacturingStage}/toggle-status',
        [ManufacturingStageController::class, 'toggleStatus']
    )->name('manufacturing-stages.toggle-status');
});
