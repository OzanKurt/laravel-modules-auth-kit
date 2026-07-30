<?php

declare(strict_types=1);

use Kurt\Modules\AuthKit\Facades\AuthKit;

it('reads a feature flag from config', function () {
    config()->set('auth-kit.features.registration', true);
    expect(AuthKit::feature('registration'))->toBeTrue();

    config()->set('auth-kit.features.registration', false);
    expect(AuthKit::feature('registration'))->toBeFalse();
});

it('treats an unknown feature as disabled', function () {
    expect(AuthKit::feature('does_not_exist'))->toBeFalse();
});

it('coerces truthy config values to a strict boolean', function () {
    config()->set('auth-kit.features.two_factor', 1);
    expect(AuthKit::feature('two_factor'))->toBeTrue();
});
