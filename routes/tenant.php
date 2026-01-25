<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomInitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
        'web',
    CustomInitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return view('admin.main-dashboard');
    })->middleware(['auth'])->name('tenant.dashboard');

    Route::get('/inactive', function () {
        return view('tenancy::tenant-inactive');
    })->name('tenant.inactive');
});
