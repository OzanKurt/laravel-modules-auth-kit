<?php

declare(strict_types=1);

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Kurt\Modules\AuthKit\Actions\RegisterAction;

it('registers, fires Registered, and logs the user in by default', function () {
    Event::fake([Registered::class]);

    $user = app(RegisterAction::class)->handle(['name' => 'Ada', 'email' => 'ada@b.com', 'password' => 'secret12']);

    Event::assertDispatched(Registered::class);
    expect(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->getAuthIdentifier());
});

it('does not log in when login_after is disabled', function () {
    config()->set('auth-kit.register.login_after', false);
    Event::fake([Registered::class]);

    app(RegisterAction::class)->handle(['name' => 'Ada', 'email' => 'ada@b.com', 'password' => 'secret12']);

    expect(Auth::check())->toBeFalse();
});
