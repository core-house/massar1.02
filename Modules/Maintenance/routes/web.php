<?php

use Illuminate\Support\Facades\Route;
use Modules\Maintenance\Http\Controllers\MaintenanceStatisticsController;
use Modules\Maintenance\Http\Controllers\{MaintenanceController, ServiceTypeController};

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('service-types', ServiceTypeController::class)->names('service.types');
    Route::resource('maintenances', MaintenanceController::class)->names('maintenances');

    Route::prefix('maintenance')->name('maintenance.')->group(function () {

        Route::get('/dashboard', [MaintenanceStatisticsController::class, 'index'])
            ->name('dashboard.index');
    });
});
