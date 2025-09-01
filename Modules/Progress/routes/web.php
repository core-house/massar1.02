<?php

use Illuminate\Support\Facades\Route;
use Modules\Progress\Http\Controllers\ProjectTypeController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('project-types', ProjectTypeController::class)->names('project.types');
});
