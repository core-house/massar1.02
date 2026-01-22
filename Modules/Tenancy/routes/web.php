<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenancy\Http\Controllers\TenancyController;
use Modules\Tenancy\Http\Controllers\PlanController;
use Modules\Tenancy\Http\Controllers\SubscriptionController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('tenancies', TenancyController::class)->names('tenancy');
    Route::resource('plans', PlanController::class);
    Route::resource('subscriptions', SubscriptionController::class);

    Route::patch('tenancies/{tenancy}/toggle-status', [TenancyController::class, 'toggleStatus'])->name('tenancy.toggle-status');
    Route::patch('plans/{plan}/toggle-status', [PlanController::class, 'toggleStatus'])->name('plans.toggle-status');
    Route::patch('subscriptions/{subscription}/toggle-status', [SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle-status');

    Route::get('tenancies/{tenancy}/redirect', [TenancyController::class, 'redirectToTenant'])
        ->name('tenancy.redirect');
});
