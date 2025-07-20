<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::get('/reports/accounts-tree', [ReportController::class, 'accountsTree'])->name('accounts.tree');
