<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

it('logs in via the API mode with a JSON envelope', function () {
    createUser(['email' => 'a@b.com', 'password' => Hash::make('secret12')]);

    $this->postJson(route('auth-kit.login.attempt'), ['email' => 'a@b.com', 'password' => 'secret12'])
        ->assertOk()
        ->assertJsonPath('data.authenticated', true);

    expect(Auth::check())->toBeTrue();
});

it('returns a 422 JSON error envelope for bad credentials (no enumeration)', function () {
    createUser(['email' => 'a@b.com', 'password' => Hash::make('secret12')]);
    $failed = trans('auth-kit::auth.failed');

    $this->postJson(route('auth-kit.login.attempt'), ['email' => 'a@b.com', 'password' => 'nope'])
        ->assertStatus(422)
        ->assertJsonPath('errors.email.0', $failed);

    $this->postJson(route('auth-kit.login.attempt'), ['email' => 'ghost@b.com', 'password' => 'nope'])
        ->assertStatus(422)
        ->assertJsonPath('errors.email.0', $failed);

    expect(Auth::check())->toBeFalse();
});

it('logs out via the API mode with a 204 response', function () {
    $user = createUser(['email' => 'a@b.com', 'password' => Hash::make('secret12')]);

    $this->actingAs($user)
        ->postJson(route('auth-kit.logout'))
        ->assertNoContent();

    expect(Auth::check())->toBeFalse();
});
