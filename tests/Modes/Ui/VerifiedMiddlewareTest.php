<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('_protected', fn () => 'ok')->middleware(['web', 'auth', 'auth-kit.verified']);
});

it('redirects an unverified user away from a verified-only route', function () {
    $this->actingAs(createUser(['email_verified_at' => null]));
    $this->get('_protected')->assertRedirect(route('auth-kit.verification.notice'));
});

it('lets a verified user through', function () {
    $this->actingAs(createUser(['email_verified_at' => now()]));
    $this->get('_protected')->assertOk()->assertSee('ok');
});
