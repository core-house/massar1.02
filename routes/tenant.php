<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomInitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    CustomInitializeTenancyByDomain::class,
    'web',
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return view('admin.main-dashboard');
    })->middleware(['auth'])->name('tenant.dashboard');
});
