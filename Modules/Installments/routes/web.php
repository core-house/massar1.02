<?php

use Illuminate\Support\Facades\Route;
use Modules\Installments\Http\Controllers\InstallmentController;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::prefix('installments')->name('installments.')->group(function () {
        Route::get('/plans', [InstallmentController::class, 'index'])->name('plans.index');
        Route::get('/plans/create', [InstallmentController::class, 'create'])->name('plans.create');
        Route::get('/plans/{id}', [InstallmentController::class, 'show'])->name('plans.show');

        Route::get('/overdue-payments', [InstallmentController::class, 'overduePayments'])->name('payments.overdue');
    });
});
