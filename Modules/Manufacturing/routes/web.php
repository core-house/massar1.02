<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\ManufacturingOrderController;
use Modules\Manufacturing\Http\Controllers\{ManufacturingController, ManufacturingStageController};

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/manufacturing/statistics', [ManufacturingController::class, 'manufacturingStatistics'])->name('manufacturing.statistics');

    Route::resource('manufacturing', ManufacturingController::class)->names('manufacturing');
    Route::resource('manufacturing-stages', ManufacturingStageController::class)->names('manufacturing.stages');
    Route::resource('manufacturing-orders', ManufacturingOrderController::class)->names('manufacturing.orders');

    Route::patch(
        'manufacturing-stages/{manufacturingStage}/toggle-status',
        [ManufacturingStageController::class, 'toggleStatus']
    )->name('manufacturing-stages.toggle-status');

    // Route::middleware(['auth'])->prefix('manufacturing')->name('manufacturing.')->group(function () {
    //     Route::get('/', [ManufacturingController::class, 'index'])->name('index');
    //     Route::get('/create', [ManufacturingController::class, 'create'])->name('create');
    //     Route::get('/{id}/edit', [ManufacturingController::class, 'edit'])->name('edit');
    //     Route::get('/{id}', [ManufacturingController::class, 'show'])->name('show');
    //     Route::get('/statistics', [ManufacturingController::class, 'manufacturingStatistics'])->name('statistics');
    // });
});
