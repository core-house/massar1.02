<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounts\Http\Controllers\AccountsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('accounts', AccountsController::class)->names('accounts');
});
