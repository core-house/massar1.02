<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use Modules\Accounts\Http\Controllers\Api\AccHeadApiController;

// API routes for Accounts module
Route::prefix('v1')->group(function () {
    Route::get('accounts', [AccHeadApiController::class, 'index'])->name('accounts.index');
});
