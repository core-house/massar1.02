<?php

use Illuminate\Support\Facades\Route;
use Modules\Progress\Http\Controllers\{
    DailyProgressController,
    WorkItemController,
    ProjectItemController,
    ProjectTypeController,
    ProjectTemplateController,
    ProjectProgressController
};

Route::middleware(['auth'])->group(function () {
    Route::resource('project-types', ProjectTypeController::class)->names('project.types');
    Route::resource('work-items', WorkItemController::class)->names('work.items');
    Route::resource('project-template', ProjectTemplateController::class)->names('project.template');
    // Route::resource('project-items', ProjectItemController::class)->names('project.items');
    Route::resource('progress-projcet', ProjectProgressController::class)->names('progress.projcet');
    Route::resource('daily-progress', DailyProgressController::class)->names('daily.progress');

    Route::prefix('projects/{project}')->middleware('auth')->group(function () {
        Route::post('/items', [ProjectItemController::class, 'store'])->name('project-items.store');
        Route::put('/items/{projectItem}', [ProjectItemController::class, 'update'])->name('project-items.update');
        Route::delete('/items/{projectItem}', [ProjectItemController::class, 'destroy'])->name('project-items.destroy');
    });
    Route::get('/projects/progress/{project}', [ProjectProgressController::class, 'progress'])
        ->name('projects.progress/state');

    Route::get('/daily-progress/executed-today', [DailyProgressController::class, 'executedToday']);
});
