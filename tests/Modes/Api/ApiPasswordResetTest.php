<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Notification;

it('returns the same JSON envelope for known and unknown emails', function () {
    Notification::fake();
    createUser(['email' => 'a@b.com']);

    $known = $this->postJson(route('auth-kit.password.email'), ['email' => 'a@b.com'])->assertOk();
    $unknown = $this->postJson(route('auth-kit.password.email'), ['email' => 'ghost@b.com'])->assertOk();

    expect($unknown->json())->toBe($known->json());
});
