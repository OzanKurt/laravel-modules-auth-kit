<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kurt\Modules\AuthKit\Http\Controllers\LoginController;

// API mode: JSON login/logout (session-cookie based; token auth is a later milestone).
Route::post('login', [LoginController::class, 'login'])->name('auth-kit.login.attempt');
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('auth-kit.logout');
