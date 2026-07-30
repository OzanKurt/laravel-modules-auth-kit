<?php

declare(strict_types=1);

use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

it('verifies the email via a valid signed link and fires Verified', function () {
    Event::fake([Verified::class]);
    $user = createUser(['email' => 'a@b.com', 'email_verified_at' => null]);
    $this->actingAs($user);

    $url = URL::temporarySignedRoute('auth-kit.verification.verify', now()->addMinutes(60), [
        'id' => $user->getKey(),
        'hash' => sha1($user->getEmailForVerification()),
    ]);

    $this->get($url)->assertRedirect();

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    Event::assertDispatched(Verified::class);
});

it('shows the notice page to an unverified user', function () {
    $this->actingAs(createUser(['email_verified_at' => null]));

    $this->get(route('auth-kit.verification.notice'))
        ->assertOk()
        ->assertViewIs('auth-kit::auth.verify-email');
});

it('rejects a tampered hash', function () {
    $user = createUser(['email' => 'a@b.com', 'email_verified_at' => null]);
    $this->actingAs($user);

    $url = URL::temporarySignedRoute('auth-kit.verification.verify', now()->addMinutes(60), [
        'id' => $user->getKey(),
        'hash' => sha1('someone-elses-email'),
    ]);

    $this->get($url)->assertForbidden();
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('resends the verification notification', function () {
    Notification::fake();
    $user = createUser(['email' => 'a@b.com', 'email_verified_at' => null]);
    $this->actingAs($user);

    $this->post(route('auth-kit.verification.send'))->assertRedirect();

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('throttles the resend endpoint', function () {
    Notification::fake();
    $user = createUser(['email' => 'a@b.com', 'email_verified_at' => null]);
    $this->actingAs($user);

    // throttle:6,1 -> the 7th request within the window is rejected.
    for ($i = 0; $i < 6; $i++) {
        $this->post(route('auth-kit.verification.send'))->assertRedirect();
    }

    $this->post(route('auth-kit.verification.send'))->assertStatus(429);
});
