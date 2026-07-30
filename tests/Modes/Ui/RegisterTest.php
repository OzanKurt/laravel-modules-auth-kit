<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;

it('registers a user via the UI and logs them in', function () {
    $this->post(route('auth-kit.register.attempt'), [
        'name' => 'Ada', 'email' => 'ada@b.com',
        'password' => 'secret12', 'password_confirmation' => 'secret12',
    ])->assertRedirect();

    expect(Auth::check())->toBeTrue();
});

it('rejects a mismatched password confirmation', function () {
    $this->from(route('auth-kit.register'))->post(route('auth-kit.register.attempt'), [
        'name' => 'Ada', 'email' => 'ada@b.com',
        'password' => 'secret12', 'password_confirmation' => 'nope',
    ])->assertSessionHasErrors('password');

    expect(Auth::check())->toBeFalse();
});
