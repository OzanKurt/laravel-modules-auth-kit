<?php

declare(strict_types=1);

use Illuminate\Support\Facades\URL;

it('verifies via the API and returns a JSON envelope', function () {
    $user = createUser(['email' => 'a@b.com', 'email_verified_at' => null]);
    $this->actingAs($user);

    $url = URL::temporarySignedRoute('auth-kit.verification.verify', now()->addMinutes(60), [
        'id' => $user->getKey(),
        'hash' => sha1($user->getEmailForVerification()),
    ]);

    $this->getJson($url)->assertOk()->assertJsonPath('data.verified', true);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});
