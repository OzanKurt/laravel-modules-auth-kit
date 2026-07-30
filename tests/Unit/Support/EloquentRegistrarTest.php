<?php

declare(strict_types=1);

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Kurt\Modules\AuthKit\Contracts\Registrar;

it('creates a user with a hashed password and configured fields', function () {
    $user = app(Registrar::class)->register([
        'name' => 'Ada',
        'email' => 'ada@b.com',
        'password' => 'secret12',
    ]);

    expect($user)->toBeInstanceOf(Authenticatable::class)
        ->and($user->getAuthIdentifier())->not->toBeNull()   // persisted
        ->and($user->email)->toBe('ada@b.com')
        ->and($user->name)->toBe('Ada')
        ->and($user->password)->not->toBe('secret12')          // hashed
        ->and(Hash::check('secret12', $user->password))->toBeTrue();
});

it('ignores data keys not in the configured fields (mass-assignment safety)', function () {
    config()->set('auth-kit.register.fields', ['name', 'email']);

    $user = app(Registrar::class)->register([
        'name' => 'Ada', 'email' => 'ada@b.com', 'password' => 'secret12',
        'is_admin' => true, // not a configured field
    ]);

    expect($user->is_admin ?? null)->not->toBeTrue();
});
