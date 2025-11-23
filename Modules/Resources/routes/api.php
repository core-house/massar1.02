<?php

use Illuminate\Support\Facades\Route;
use Modules\Resources\Http\Controllers\ResourcesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('resources', ResourcesController::class)->names('resources');
});
