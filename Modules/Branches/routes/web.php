<?php

use Illuminate\Support\Facades\Route;
use Modules\Branches\Http\Controllers\BranchesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('branches', BranchesController::class)->names('branches');
    Route::post('/branches/toggle-status', [BranchesController::class, 'toggleStatus'])
        ->name('branches.toggleStatus');
});
