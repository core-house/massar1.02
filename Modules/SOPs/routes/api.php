<?php

use Illuminate\Support\Facades\Route;
use Modules\SOPs\Http\Controllers\SOPsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sops', SOPsController::class)->names('sops');
});
