<?php

declare(strict_types=1);

use Kurt\Modules\AuthKit\AuthKitManager;
use Kurt\Modules\AuthKit\Facades\AuthKit;

it('boots the package and merges config', function () {
    expect(config('auth-kit.features.registration'))->toBeTrue();
});

it('resolves the manager singleton and facade', function () {
    expect(app('auth-kit'))->toBeInstanceOf(AuthKitManager::class)
        ->and(app('auth-kit'))->toBe(app(AuthKitManager::class))
        ->and(AuthKit::module())->toBe('auth-kit');
});
