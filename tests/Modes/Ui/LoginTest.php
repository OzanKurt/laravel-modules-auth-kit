<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Kurt\Modules\AuthKit\Tests\Fixtures\AuthKitTestUser;

it('logs in via the UI and redirects', function () {
    createUser(['email' => 'a@b.com', 'password' => Hash::make('secret12')]);

    $this->post(route('auth-kit.login.attempt'), ['email' => 'a@b.com', 'password' => 'secret12'])
        ->assertRedirect();

    expect(Auth::check())->toBeTrue();
});

it('returns a generic error for both unknown email and wrong password (no enumeration)', function () {
    createUser(['email' => 'a@b.com', 'password' => Hash::make('secret12')]);
    $failed = trans('auth-kit::auth.failed');

    // Wrong password for a real user AND an unknown email must yield the SAME message.
    $this->from(route('auth-kit.login'))
        ->post(route('auth-kit.login.attempt'), ['email' => 'a@b.com', 'password' => 'nope'])
        ->assertSessionHasErrors(['email' => $failed]);

    $this->from(route('auth-kit.login'))
        ->post(route('auth-kit.login.attempt'), ['email' => 'ghost@b.com', 'password' => 'nope'])
        ->assertSessionHasErrors(['email' => $failed]);

    expect(Auth::check())->toBeFalse();
});

it('persists a remember token only when remember is requested and allowed', function () {
    createUser(['email' => 'a@b.com', 'password' => Hash::make('secret12')]);

    // Not requested: no remember token persisted (getRememberToken casts null
    // to an empty string).
    $this->post(route('auth-kit.login.attempt'), ['email' => 'a@b.com', 'password' => 'secret12'])
        ->assertRedirect();
    expect(AuthKitTestUser::query()->first()->getRememberToken())->toBeEmpty();
    Auth::logout();

    // Requested but disallowed by config: still no remember token.
    config()->set('auth-kit.login.allow_remember', false);
    $this->post(route('auth-kit.login.attempt'), ['email' => 'a@b.com', 'password' => 'secret12', 'remember' => '1'])
        ->assertRedirect();
    expect(AuthKitTestUser::query()->first()->getRememberToken())->toBeEmpty();
    Auth::logout();

    // Requested and allowed: remember token persisted.
    config()->set('auth-kit.login.allow_remember', true);
    $this->post(route('auth-kit.login.attempt'), ['email' => 'a@b.com', 'password' => 'secret12', 'remember' => '1'])
        ->assertRedirect();
    expect(AuthKitTestUser::query()->first()->getRememberToken())->not->toBeEmpty();
});
