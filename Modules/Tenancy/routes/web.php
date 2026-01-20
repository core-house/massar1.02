<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenancy\Http\Controllers\TenancyController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('tenancies', TenancyController::class)->names('tenancy');
    Route::get('tenancies/{tenancy}/redirect', [TenancyController::class, 'redirectToTenant'])
        ->name('tenancy.redirect');
});
