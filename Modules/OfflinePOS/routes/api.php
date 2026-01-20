<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Offline POS API Routes
|--------------------------------------------------------------------------
|
| API للمزامنة وتحميل البيانات - يعمل مع stancl/tenancy
| محمي بـ Sanctum authentication
|
*/

Route::middleware([
    'api',
    \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
])->prefix('offline-pos')->name('api.offline-pos.')->group(function () {
    
    Route::middleware([
        'auth:sanctum',
        \Modules\OfflinePOS\app\Http\Middleware\EnsureBranchContext::class,
    ])->group(function () {
        
        // تحميل البيانات الأولية للعمل offline
        Route::get('/init-data', [\Modules\OfflinePOS\app\Http\Controllers\API\InitDataController::class, 'index'])
            ->name('init-data');
        
        // مزامنة معاملة واحدة
        Route::post('/sync-transaction', [\Modules\OfflinePOS\app\Http\Controllers\API\SyncController::class, 'syncTransaction'])
            ->name('sync-transaction');
        
        // مزامنة جماعية
        Route::post('/batch-sync', [\Modules\OfflinePOS\app\Http\Controllers\API\SyncController::class, 'batchSync'])
            ->name('batch-sync');
        
        // التحقق من حالة المزامنة
        Route::get('/sync-status/{localId}', [\Modules\OfflinePOS\app\Http\Controllers\API\SyncController::class, 'checkStatus'])
            ->name('sync-status');
        
        // فاتورة مرتجعة
        Route::post('/return-invoice', [\Modules\OfflinePOS\app\Http\Controllers\API\ReturnInvoiceController::class, 'create'])
            ->name('return-invoice');
        
        // التقارير
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/best-sellers', [\Modules\OfflinePOS\app\Http\Controllers\API\ReportsController::class, 'bestSellers'])
                ->name('best-sellers');
            
            Route::get('/top-customers', [\Modules\OfflinePOS\app\Http\Controllers\API\ReportsController::class, 'topCustomers'])
                ->name('top-customers');
            
            Route::get('/daily-sales', [\Modules\OfflinePOS\app\Http\Controllers\API\ReportsController::class, 'dailySales'])
                ->name('daily-sales');
            
            Route::get('/sales-summary', [\Modules\OfflinePOS\app\Http\Controllers\API\ReportsController::class, 'salesSummary'])
                ->name('sales-summary');
        });
        
    });
    
});
