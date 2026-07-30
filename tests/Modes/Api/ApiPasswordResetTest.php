<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('returns the same JSON envelope for known and unknown emails', function () {
    Notification::fake();
    createUser(['email' => 'a@b.com']);

    $known = $this->postJson(route('auth-kit.password.email'), ['email' => 'a@b.com'])->assertOk();
    $unknown = $this->postJson(route('auth-kit.password.email'), ['email' => 'ghost@b.com'])->assertOk();

    expect($unknown->json())->toBe($known->json());
});

it('resets via the API and returns a JSON envelope', function () {
    $user = createUser(['email' => 'a@b.com', 'password' => bcrypt('oldpass12')]);
    $token = Password::createToken($user);

    $this->postJson(route('auth-kit.password.update'), [
        'token' => $token,
        'email' => 'a@b.com',
        'password' => 'newpass12',
        'password_confirmation' => 'newpass12',
    ])->assertOk()->assertJsonPath('data.reset', true);

    expect(Hash::check('newpass12', $user->fresh()->password))->toBeTrue();
});

it('rejects an invalid token with a 422 and leaves the password unchanged', function () {
    $user = createUser(['email' => 'a@b.com', 'password' => bcrypt('oldpass12')]);

    $this->postJson(route('auth-kit.password.update'), [
        'token' => 'not-a-real-token',
        'email' => 'a@b.com',
        'password' => 'newpass12',
        'password_confirmation' => 'newpass12',
    ])->assertStatus(422)->assertJsonValidationErrors('email');

    expect(Hash::check('oldpass12', $user->fresh()->password))->toBeTrue();
});
