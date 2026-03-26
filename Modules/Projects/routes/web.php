<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Projects\Http\Controllers\ProjectController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('projects')->name('projects.')->group(function () {
    Route::get('/statistics', [ProjectController::class, 'statistics'])->name('statistics');
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/create', [ProjectController::class, 'create'])->name('create');
    Route::post('/', [ProjectController::class, 'store'])->name('store');
    Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
    Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
    Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
    Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
});
