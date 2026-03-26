<?php

use Illuminate\Support\Facades\Route;
use Modules\Installments\Http\Controllers\InstallmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('installments', InstallmentController::class)->names('installments');
});
