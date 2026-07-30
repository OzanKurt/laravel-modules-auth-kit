<?php

declare(strict_types=1);

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('sends a reset link for a known email', function () {
    Notification::fake();
    $user = createUser(['email' => 'a@b.com']);

    $this->post(route('auth-kit.password.email'), ['email' => 'a@b.com'])
        ->assertRedirect()
        ->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPassword::class);
});

it('responds identically for an unknown email (no enumeration)', function () {
    Notification::fake();
    createUser(['email' => 'a@b.com']);

    $known = $this->post(route('auth-kit.password.email'), ['email' => 'a@b.com']);
    $knownStatus = session('status');

    $unknown = $this->post(route('auth-kit.password.email'), ['email' => 'ghost@b.com']);
    $unknownStatus = session('status');

    expect($unknown->getStatusCode())->toBe($known->getStatusCode())
        ->and($unknown->headers->get('Location'))->toBe($known->headers->get('Location'))
        ->and($unknownStatus)->toBe($knownStatus);

    $unknown->assertSessionHasNoErrors();
    $unknown->assertSessionHas('status');
});

it('shows the forgot-password form', function () {
    $this->get(route('auth-kit.password.request'))
        ->assertOk()
        ->assertViewIs('auth-kit::auth.forgot-password');
});

it('throttles the forgot-password endpoint', function () {
    Notification::fake();
    createUser(['email' => 'a@b.com']);

    // throttle:6,1 -> the 7th request within the window is rejected.
    for ($i = 0; $i < 6; $i++) {
        $this->post(route('auth-kit.password.email'), ['email' => 'a@b.com'])->assertRedirect();
    }

    $this->post(route('auth-kit.password.email'), ['email' => 'a@b.com'])->assertStatus(429);
});

it('points the emailed link at the package reset route', function () {
    Notification::fake();
    $user = createUser(['email' => 'a@b.com']);

    $this->post(route('auth-kit.password.email'), ['email' => 'a@b.com'])->assertRedirect();

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
        return $notification->toMail($user)->actionUrl === route('auth-kit.password.reset', [
            'token' => $notification->token,
            'email' => 'a@b.com',
        ]);
    });
});

it('shows the reset form for a token', function () {
    $this->get(route('auth-kit.password.reset', ['token' => 'a-token', 'email' => 'a@b.com']))
        ->assertOk()
        ->assertViewIs('auth-kit::auth.reset-password')
        ->assertViewHas('token', 'a-token');
});

it('resets the password with a valid token', function () {
    Event::fake([PasswordReset::class]);
    $user = createUser(['email' => 'a@b.com', 'password' => bcrypt('oldpass12')]);
    $token = Password::createToken($user);

    $this->post(route('auth-kit.password.update'), [
        'token' => $token,
        'email' => 'a@b.com',
        'password' => 'newpass12',
        'password_confirmation' => 'newpass12',
    ])->assertRedirect();

    expect(Hash::check('newpass12', $user->fresh()->password))->toBeTrue();
    Event::assertDispatched(PasswordReset::class);
});

it('rejects an invalid token and leaves the password unchanged', function () {
    $user = createUser(['email' => 'a@b.com', 'password' => bcrypt('oldpass12')]);

    $this->from(route('auth-kit.password.request'))->post(route('auth-kit.password.update'), [
        'token' => 'not-a-real-token',
        'email' => 'a@b.com',
        'password' => 'newpass12',
        'password_confirmation' => 'newpass12',
    ])->assertSessionHasErrors('email');

    expect(Hash::check('oldpass12', $user->fresh()->password))->toBeTrue();
});

it('rejects a weak or unconfirmed password', function () {
    $user = createUser(['email' => 'a@b.com', 'password' => bcrypt('oldpass12')]);
    $token = Password::createToken($user);

    $this->from(route('auth-kit.password.request'))->post(route('auth-kit.password.update'), [
        'token' => $token,
        'email' => 'a@b.com',
        'password' => 'short',
        'password_confirmation' => 'mismatch',
    ])->assertSessionHasErrors('password');

    expect(Hash::check('oldpass12', $user->fresh()->password))->toBeTrue();
});

it('rotates the remember token so old sessions cannot resume', function () {
    $user = createUser(['email' => 'a@b.com', 'password' => bcrypt('oldpass12')]);
    $user->forceFill(['remember_token' => 'stale-remember-token'])->save();
    $token = Password::createToken($user);

    $this->post(route('auth-kit.password.update'), [
        'token' => $token,
        'email' => 'a@b.com',
        'password' => 'newpass12',
        'password_confirmation' => 'newpass12',
    ])->assertRedirect();

    expect($user->fresh()->getRememberToken())->not->toBe('stale-remember-token');
});

it('throttles the reset endpoint', function () {
    createUser(['email' => 'a@b.com', 'password' => bcrypt('oldpass12')]);

    $payload = [
        'token' => 'not-a-real-token',
        'email' => 'a@b.com',
        'password' => 'newpass12',
        'password_confirmation' => 'newpass12',
    ];

    // throttle:6,1 -> the 7th request within the window is rejected.
    for ($i = 0; $i < 6; $i++) {
        $this->from(route('auth-kit.password.request'))
            ->post(route('auth-kit.password.update'), $payload)
            ->assertSessionHasErrors('email');
    }

    $this->post(route('auth-kit.password.update'), $payload)->assertStatus(429);
});
