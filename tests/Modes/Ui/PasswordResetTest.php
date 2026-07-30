<?php

declare(strict_types=1);

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

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
