<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChequeController;
Route::middleware(['module.access:checks'])->group(function () {
    Route::resource('cheques', ChequeController::class)->names('cheques');
});
