<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kurt\Modules\AuthKit\Facades\AuthKit;
use Kurt\Modules\AuthKit\Http\Controllers\EmailVerificationController;
use Kurt\Modules\AuthKit\Http\Controllers\LoginController;
use Kurt\Modules\AuthKit\Http\Controllers\RegisterController;

// API mode: JSON login/logout (session-cookie based; token auth is a later milestone).
Route::post('login', [LoginController::class, 'login'])->name('auth-kit.login.attempt');
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('auth-kit.logout');

if (AuthKit::feature('registration')) {
    Route::post('register', [RegisterController::class, 'register'])->name('auth-kit.register.attempt');
}

if (AuthKit::feature('email_verification')) {
    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['auth', 'signed', 'throttle:6,1'])->name('auth-kit.verification.verify');
    Route::post('email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware(['auth', 'throttle:6,1'])->name('auth-kit.verification.send');
}
