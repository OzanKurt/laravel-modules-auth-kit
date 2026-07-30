<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Kurt\Modules\AuthKit\Actions\LoginAction;

it('authenticates valid credentials and regenerates the session', function () {
    $user = createUser(['email' => 'a@b.com', 'password' => Hash::make('secret12')]);

    $before = session()->getId();
    $ok = app(LoginAction::class)->handle(['email' => 'a@b.com', 'password' => 'secret12']);

    expect($ok)->toBeTrue()
        ->and(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->getKey())
        ->and(session()->getId())->not->toBe($before); // fixation defence
});

it('rejects wrong credentials without logging in', function () {
    createUser(['email' => 'a@b.com', 'password' => Hash::make('secret12')]);

    expect(app(LoginAction::class)->handle(['email' => 'a@b.com', 'password' => 'wrong']))->toBeFalse()
        ->and(Auth::check())->toBeFalse();
});
