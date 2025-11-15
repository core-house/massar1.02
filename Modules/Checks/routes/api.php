<?php

use Illuminate\Support\Facades\Route;
use Modules\Checks\Http\Controllers\ChecksController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('checks')->name('api.checks.')->group(function () {
        Route::get('/statistics', [ChecksController::class, 'statistics'])->name('statistics');
    });
});