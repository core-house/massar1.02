<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\UniversalSearch\Http\Controllers\UniversalSearchController;

Route::middleware(['auth'])
    ->prefix('universal-search')
    ->name('universalsearch.')
    ->group(function () {
        Route::get('/search', [UniversalSearchController::class, 'search'])->name('search');
    });

