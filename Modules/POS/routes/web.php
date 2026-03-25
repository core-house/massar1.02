<?php

use Illuminate\Support\Facades\Route;
use Modules\POS\app\Http\Controllers\POSController;
use Modules\POS\app\Http\Controllers\KitchenPrinterStationController;
use Modules\POS\app\Http\Controllers\PrintJobController;
use Modules\POS\app\Http\Controllers\SetupController;
use Modules\POS\app\Http\Controllers\DriverController;
use Modules\POS\app\Http\Controllers\DeliveryAreaController;
use Modules\POS\app\Http\Controllers\RestaurantTableController;

/*
|--------------------------------------------------------------------------
| POS Module Routes
|--------------------------------------------------------------------------
|
| نظام نقاط البيع - المسارات الخاصة بوحدة POS
| جميع المسارات محمية بـ middleware للمصادقة والصلاحيات
|
*/

// Service Worker + Ping - public, no auth required
Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/service-worker.js', function () {
        $path = public_path('modules/pos/js/pos-service-worker.js');
        return response()->file($path, [
            'Content-Type' => 'application/javascript',
            'Service-Worker-Allowed' => '/pos/',
        ]);
    })->name('service-worker');

    // Ping - لا يحتاج auth، فقط للتحقق من وصول الشبكة للسيرفر
    Route::get('/api/ping', fn() => response()->json(['ok' => true]))->name('api.ping');
});

Route::middleware(['auth', 'verified', \Modules\POS\app\Http\Middleware\SafeSearchMiddleware::class])->prefix('pos')->name('pos.')->group(function () {

    // الصفحة الرئيسية لنظام POS
    Route::get('/', [POSController::class, 'index'])
        ->name('index')
        ->middleware('can:view POS System');

    // إنشاء معاملة POS جديدة (كاشير)
    Route::get('/create', [POSController::class, 'create'])
        ->name('create')
        ->middleware('can:create POS Transaction');

    // واجهة المطعم
    Route::get('/restaurant', [POSController::class, 'restaurant'])
        ->name('restaurant')
        ->middleware('can:create POS Transaction');

    // عرض معاملة POS محددة
    Route::get('/show/{id}', [POSController::class, 'show'])
        ->name('show')
        ->middleware('can:view POS Transaction');

    // تحرير معاملة POS
    Route::get('/edit/{id}', [POSController::class, 'edit'])
        ->name('edit')
        ->middleware('can:edit POS Transaction');

    // تحديث معاملة POS
    Route::put('/update/{id}', [POSController::class, 'update'])
        ->name('update')
        ->middleware('can:edit POS Transaction');

    // طباعة فاتورة POS
    Route::get('/print/{operation_id}', [POSController::class, 'print'])
        ->name('print')
        ->middleware('can:print POS Transaction');

    // حذف معاملة POS
    Route::delete('/delete/{id}', [POSController::class, 'destroy'])
        ->name('destroy')
        ->middleware('can:delete POS Transaction');

    // تقارير POS
    Route::get('/reports', [POSController::class, 'reports'])
        ->name('reports')
        ->middleware('can:view POS Reports');

    // إعدادات الكاشير
    Route::get('/settings', [POSController::class, 'settings'])
        ->name('settings')
        ->middleware('can:view POS System');

    // شاشة فحص السعر بالباركود
    Route::get('/price-check', [POSController::class, 'priceCheck'])
        ->name('price-check')
        ->middleware('can:view POS System');

    // AJAX Routes
    Route::get('/api/search-items', [POSController::class, 'searchItems'])->name('api.search-items');
    Route::get('/api/search-barcode', [POSController::class, 'searchByBarcode'])->name('api.search-barcode');
    Route::get('/api/price-check/{barcode}', [POSController::class, 'getPriceByBarcode'])->name('api.price-check');
    Route::get('/api/item/{id}', [POSController::class, 'getItemDetails'])->name('api.item-details');
    Route::get('/api/category/{categoryId}/items', [POSController::class, 'getCategoryItems'])->name('api.category-items');
    Route::get('/api/items-details', [POSController::class, 'getAllItemsDetails'])->name('api.all-items-details');
    Route::get('/api/customer/{id}/balance', [POSController::class, 'getCustomerBalance'])->name('api.customer-balance');
    Route::get('/api/search-customer-phone', [POSController::class, 'searchCustomerByPhone'])->name('api.search-customer-phone');
    Route::post('/api/save-delivery-customer', [POSController::class, 'saveDeliveryCustomer'])->name('api.save-delivery-customer');
    Route::post('/api/update-delivery-customer-address', [POSController::class, 'updateDeliveryCustomerAddress'])->name('api.update-delivery-customer-address');
    Route::get('/api/customer/{id}/recommendations', [POSController::class, 'getCustomerRecommendations'])->name('api.customer-recommendations');
    Route::get('/api/recent-transactions', [POSController::class, 'getRecentTransactions'])->name('api.recent-transactions');
    Route::post('/api/store', [POSController::class, 'store'])->name('api.store');
    Route::post('/api/sync', [POSController::class, 'syncTransactions'])->name('api.sync');
    Route::post('/api/settings/update', [POSController::class, 'updateSettings'])->name('api.settings.update');
    Route::get('/api/settings/scale', [POSController::class, 'getScaleSettings'])->name('api.settings.scale');

    // Hold/Suspend Order Routes
    Route::post('/api/hold-order', [POSController::class, 'holdOrder'])->name('api.hold-order');
    Route::get('/api/held-orders', [POSController::class, 'getHeldOrders'])->name('api.held-orders');
    Route::get('/api/recall-order/{id}', [POSController::class, 'recallOrder'])->name('api.recall-order');
    Route::post('/api/complete-held-order/{id}', [POSController::class, 'completeHeldOrder'])->name('api.complete-held-order');
    Route::delete('/api/delete-held-order/{id}', [POSController::class, 'deleteHeldOrder'])->name('api.delete-held-order');

    // Petty Cash (المصروفات النثرية)
    Route::post('/api/petty-cash', [POSController::class, 'pettyCash'])->name('api.petty-cash');

    // Return Invoice
    Route::get('/api/invoice/{proId}', [POSController::class, 'getInvoice'])->name('api.invoice');
    Route::post('/api/return-invoice', [POSController::class, 'returnInvoice'])->name('api.return-invoice');

    // POS Setup
    Route::get('/setup', [SetupController::class, 'index'])->name('setup.index')->middleware('can:view POS System');
    Route::resource('drivers', DriverController::class)->except(['create', 'show']);
    Route::resource('delivery-areas', DeliveryAreaController::class)->except(['create', 'show']);
    Route::resource('restaurant-tables', RestaurantTableController::class)->except(['create', 'show']);

    // Kitchen Printer Stations Management
    Route::resource('kitchen-printers', KitchenPrinterStationController::class)
        ->except(['show'])
        ->names([
            'index' => 'kitchen-printers.index',
            'create' => 'kitchen-printers.create',
            'store' => 'kitchen-printers.store',
            'edit' => 'kitchen-printers.edit',
            'update' => 'kitchen-printers.update',
            'destroy' => 'kitchen-printers.destroy',
        ]);

    // Print Jobs Management
    Route::prefix('print-jobs')->name('print-jobs.')->group(function () {
        Route::get('/', [PrintJobController::class, 'index'])->name('index');
        Route::get('/{printJob}', [PrintJobController::class, 'show'])->name('show');
        Route::post('/{printJob}/retry', [PrintJobController::class, 'retry'])->name('retry');
        Route::post('/batch-retry', [PrintJobController::class, 'batchRetry'])->name('batch-retry');
        Route::get('/monitoring', [PrintJobController::class, 'monitoring'])->name('monitoring');

        // Test route (temporary)
        Route::get('/test-reliability', function () {
            $results = [];

            // Test 1: Check new columns
            $results['columns'] = [];
            $columns = ['idempotency_key', 'payload_hash', 'sequence', 'error_type', 'sent_at', 'can_auto_retry'];
            foreach ($columns as $col) {
                $results['columns'][$col] = Schema::hasColumn('print_jobs', $col);
            }

            // Test 2: Test idempotency key generation
            $results['idempotency_key'] = \Modules\POS\Models\PrintJob::generateIdempotencyKey(1, 2, 'test', 1);

            // Test 3: Test payload hash
            $results['payload_hash'] = \Modules\POS\Models\PrintJob::generatePayloadHash('test content');

            // Test 4: Get KPIs
            try {
                $service = app(\Modules\POS\Services\PrintJobMonitoringService::class);
                $results['kpis'] = $service->getKPIs(24);
            } catch (\Exception $e) {
                $results['kpis_error'] = $e->getMessage();
            }

            // Test 5: Count print jobs
            $results['total_jobs'] = \Modules\POS\Models\PrintJob::count();

            return response()->json($results, 200, [], JSON_PRETTY_PRINT);
        })->name('test-reliability');
    });

});
