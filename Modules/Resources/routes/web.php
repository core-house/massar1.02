<?php

use Illuminate\Support\Facades\Route;
use Modules\Resources\Http\Controllers\ResourceController;
use Modules\Resources\Http\Controllers\ResourceCategoryController;
use Modules\Resources\Http\Controllers\ResourceTypeController;
use Modules\Resources\Http\Controllers\ResourceStatusController;
use Modules\Resources\Http\Controllers\ResourceAssignmentController;
use Modules\Resources\Http\Controllers\ResourceDashboardController;

Route::middleware(['auth', 'verified'])->prefix('resources')->name('resources.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ResourceDashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('can:view Resources Dashboard');

    // Resources
    Route::resource('/', ResourceController::class)->parameters(['' => 'resource'])->except(['index', 'show']);
    Route::get('/', [ResourceController::class, 'index'])->name('index')->middleware('can:view Resources');
    Route::get('/{resource}', [ResourceController::class, 'show'])->name('show')->middleware('can:view Resources');
    
    // API route for getting types by category
    Route::get('/api/types-by-category', [ResourceController::class, 'getTypesByCategory'])->name('api.types-by-category');

    // Categories
    Route::resource('/categories', ResourceCategoryController::class)
        ->except(['show'])
        ->middleware('can:view Resource Categories');

    // Types
    Route::resource('/types', ResourceTypeController::class)
        ->except(['show'])
        ->middleware('can:view Resource Types');

    // Statuses
    Route::resource('/statuses', ResourceStatusController::class)
        ->except(['show'])
        ->middleware('can:view Resource Statuses');

    // Assignments
    Route::resource('/assignments', ResourceAssignmentController::class)
        ->middleware('can:view Resource Assignments');
});
