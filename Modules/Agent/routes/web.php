<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Agent Module Web Routes
|--------------------------------------------------------------------------
|
| Routes for the Agent module - intelligent query system for answering
| user questions about project information.
|
*/

Route::middleware(['auth', 'can:access-agent'])->prefix('agent')->name('agent.')->group(function () {
    // Ask Question Page (Main Interface)
    Volt::route('/', 'livewire.ask-question')->name('index');

    // Question History Page
    Volt::route('/history', 'livewire.question-history')->name('history');
});
