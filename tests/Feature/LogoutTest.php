<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Kurt\Modules\AuthKit\Actions\LogoutAction;

it('logs the user out and invalidates the session', function () {
    $user = createUser(['email' => 'a@b.com']);
    Auth::login($user);
    expect(Auth::check())->toBeTrue();

    $token = session()->token();
    app(LogoutAction::class)->handle();

    expect(Auth::check())->toBeFalse()
        ->and(session()->token())->not->toBe($token); // token regenerated
});
