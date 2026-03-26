<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\SOPs\Http\Controllers\SOPsController;
use Modules\SOPs\Http\Controllers\SOPCategoryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('sops', SOPsController::class)->names('sops');
    Route::resource('sop-categories', SOPCategoryController::class)->names('sop-categories');
});
