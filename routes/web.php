<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kurt\Modules\AuthKit\Facades\AuthKit;
use Kurt\Modules\AuthKit\Http\Controllers\LoginController;
use Kurt\Modules\AuthKit\Http\Controllers\RegisterController;

Route::middleware('web')->group(function () {
    Route::get('login', [LoginController::class, 'showForm'])->middleware('guest')->name('auth-kit.login');
    Route::post('login', [LoginController::class, 'login'])->middleware('guest')->name('auth-kit.login.attempt');
    Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('auth-kit.logout');

    if (AuthKit::feature('registration')) {
        Route::get('register', [RegisterController::class, 'showForm'])->middleware('guest')->name('auth-kit.register');
        Route::post('register', [RegisterController::class, 'register'])->middleware('guest')->name('auth-kit.register.attempt');
    }
});
