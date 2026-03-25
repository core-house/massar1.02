<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Decumintations\Http\Controllers\DocumentCategoryController;
use Modules\Decumintations\Http\Controllers\DocumentController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::resource('document-categories', DocumentCategoryController::class)
        ->names('document-categories');

    Route::resource('documents', DocumentController::class)
        ->names('documents');

    Route::get('documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');
});
