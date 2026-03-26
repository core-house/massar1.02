<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\{ManufacturingController, ManufacturingStageController, ManufacturingOrderController};

Route::middleware(['auth', 'verified', 'module.access:manufacturing'])->group(function () {

    // Specific routes must come BEFORE resource routes to avoid conflicts
    Route::get('/manufacturing/statistics', [ManufacturingController::class, 'manufacturingStatistics'])->name('manufacturing.statistics');

    Route::get(
        'manufacturing/stage-invoices-report',
        [ManufacturingController::class, 'stageInvoicesReport']
    )->name('manufacturing.stage-invoices-report');

    // Manufacturing Form AJAX routes (no middleware for AJAX calls)
    Route::prefix('manufacturing/api')->name('manufacturing.api.')->group(function () {
        Route::get('all-items', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'getAllItems'])
            ->name('all-items');
        Route::get('search-products', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'searchProducts'])
            ->name('search-products');
        Route::get('search-raw-materials', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'searchRawMaterials'])
            ->name('search-raw-materials');
        Route::get('get-item-units/{id}', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'getItemWithUnits'])
            ->name('get-item-units');
        Route::get('get-available-stock', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'getAvailableStock'])
            ->name('get-available-stock');
        Route::get('active-templates', [\Modules\Manufacturing\Http\Controllers\ManufacturingTemplateController::class, 'apiActiveTemplates'])
            ->name('active-templates');
        Route::get('check-duplicate', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'checkDuplicateInvoice'])
            ->name('check-duplicate');
        Route::get('check-mo-quantity', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'checkMOQuantity'])
            ->name('check-mo-quantity');
        Route::get('check-bom', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'checkBOM'])
            ->name('check-bom');
        Route::get('validate-accounts', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'validateAccounts'])
            ->name('validate-accounts');
        Route::get('get-bom', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'getBOM'])
            ->name('get-bom');
        Route::get('check-accounting-period', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'checkAccountingPeriod'])
            ->name('check-accounting-period');
        Route::get('get-tolerance-setting', [\Modules\Manufacturing\Http\Controllers\ManufacturingFormController::class, 'getToleranceSetting'])
            ->name('get-tolerance-setting');
    });

    // Manufacturing Templates
    Route::get('manufacturing/templates', [\Modules\Manufacturing\Http\Controllers\ManufacturingTemplateController::class, 'index'])
        ->name('manufacturing.templates.index');
    Route::get('manufacturing/templates/{templateId}/data', [\Modules\Manufacturing\Http\Controllers\ManufacturingTemplateController::class, 'getTemplateData'])
        ->name('manufacturing.templates.data');
    Route::get('manufacturing/templates/{templateId}/edit', [\Modules\Manufacturing\Http\Controllers\ManufacturingTemplateController::class, 'edit'])
        ->name('manufacturing.templates.edit');
    Route::put('manufacturing/templates/{templateId}', [\Modules\Manufacturing\Http\Controllers\ManufacturingTemplateController::class, 'update'])
        ->name('manufacturing.templates.update');
    Route::patch('manufacturing/templates/{templateId}/toggle-active', [\Modules\Manufacturing\Http\Controllers\ManufacturingTemplateController::class, 'toggleActive'])
        ->name('manufacturing.templates.toggle-active');
    Route::delete('manufacturing/templates/{templateId}', [\Modules\Manufacturing\Http\Controllers\ManufacturingTemplateController::class, 'destroy'])
        ->name('manufacturing.templates.destroy');

    // Resource routes
    Route::resource('manufacturing', ManufacturingController::class)->names('manufacturing');
    Route::resource('manufacturing-stages', ManufacturingStageController::class)->names('manufacturing.stages');
    Route::resource('manufacturing-orders', ManufacturingOrderController::class)->names('manufacturing.orders');

    Route::patch(
        'manufacturing-stages/{manufacturingStage}/toggle-status',
        [ManufacturingStageController::class, 'toggleStatus']
    )->name('manufacturing-stages.toggle-status');
});
