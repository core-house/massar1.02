<?php

use Illuminate\Support\Facades\Route;
use Modules\OfflinePOS\Http\Controllers\OfflinePOSController;

/*
|--------------------------------------------------------------------------
| Offline POS Routes
|--------------------------------------------------------------------------
|
| نظام نقاط البيع الأوفلاين - يعمل مع stancl/tenancy
| جميع المسارات محمية بـ:
| - InitializeTenancyByDomain (من stancl/tenancy)
| - auth & verified
| - EnsureBranchContext (للتحقق من الفرع)
| - CheckOfflinePOSPermission (للصلاحيات)
|
*/

// ✅ Tenancy middleware applied globally in bootstrap/app.php
Route::middleware('web')->group(function () {
    
    Route::prefix('offline-pos')->name('offline-pos.')->middleware([
        'auth',
        'verified',
        \Modules\OfflinePOS\Http\Middleware\EnsureBranchContext::class,
        \Modules\OfflinePOS\Http\Middleware\CheckOfflinePOSPermission::class,
    ])->group(function () {
        
        // الصفحة الرئيسية
        Route::get('/', [OfflinePOSController::class, 'index'])->name('index');
        
        // صفحة التثبيت وتنزيل البيانات
        Route::get('/install', [OfflinePOSController::class, 'install'])->name('install');
        
        // شاشة نقاط البيع
        Route::get('/pos', [OfflinePOSController::class, 'pos'])->name('pos');
        
        // عرض معاملة
        Route::get('/transactions/{id}', [OfflinePOSController::class, 'show'])->name('transactions.show');
        
        // التقارير
        Route::get('/reports', [OfflinePOSController::class, 'reports'])->name('reports');
        
        // صفحة offline
        Route::get('/offline', [OfflinePOSController::class, 'offline'])->name('offline');
        
    });
    
});
