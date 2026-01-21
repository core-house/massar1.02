<?php

use App\Http\Controllers\Auth\LoginController;         //
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// صفحات GET (تعرض الـ form فقط - بدون Volt)
Route::middleware('guest')->group(function () {

    // Login page
    Route::get('login', function () {
        return view('auth.login');           // ← الـ blade اللي عندك
    })->name('login');

    // Register page
    Route::get('register', function () {
        return view('auth.register');
    })->name('register');

    // Forgot Password page
    Route::get('forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    // Reset Password page (مع token)
    Route::get('reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
});

// صفحات محمية بـ auth
Route::middleware('auth')->group(function () {

    // Verify Email notice page
    Route::get('verify-email', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // Verify Email link (GET مع signed)
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Confirm Password page (لو مستخدم)
    Route::get('confirm-password', function () {
        return view('auth.confirm-password');
    })->name('password.confirm');
});

// POST actions (دول اللي هتتعامل معاهم في AJAX)
Route::post('login', [LoginController::class, 'login'])->name('login');           // ← controller اللي عندك
Route::post('logout', [LoginController::class, 'logout'])->name('logout');        // أو أي controller
