<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\HelpCenter\Http\Controllers\HelpCenterController;
use Modules\HelpCenter\Http\Controllers\Admin\HelpAdminController;

Route::prefix('help')->name('helpcenter.')->group(function () {

    // ── Public (no auth required) ────────────────────────────────
    Route::middleware('web')->group(function () {
        Route::get('/',                       [HelpCenterController::class, 'index'])->name('index');
        Route::get('/category/{slug}',        [HelpCenterController::class, 'category'])->name('category');
        Route::get('/article/{id}',           [HelpCenterController::class, 'article'])->name('article');
        Route::get('/search',                 [HelpCenterController::class, 'search'])->name('search');
        Route::get('/by-route',               [HelpCenterController::class, 'byRoute'])->name('by-route');
        Route::post('/article/{id}/feedback', [HelpCenterController::class, 'feedback'])->name('feedback');
    });

    // ── Admin (auth required) ────────────────────────────────────
    Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/categories',                    [HelpAdminController::class, 'categories'])->name('categories');
        Route::post('/categories',                   [HelpAdminController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}',         [HelpAdminController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}',      [HelpAdminController::class, 'destroyCategory'])->name('categories.destroy');

        Route::get('/articles',                      [HelpAdminController::class, 'articles'])->name('articles');
        Route::get('/articles/create',               [HelpAdminController::class, 'createArticle'])->name('articles.create');
        Route::post('/articles',                     [HelpAdminController::class, 'storeArticle'])->name('articles.store');
        Route::get('/articles/{article}/edit',       [HelpAdminController::class, 'editArticle'])->name('articles.edit');
        Route::put('/articles/{article}',            [HelpAdminController::class, 'updateArticle'])->name('articles.update');
        Route::delete('/articles/{article}',         [HelpAdminController::class, 'destroyArticle'])->name('articles.destroy');
    });
});
