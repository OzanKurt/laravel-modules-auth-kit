<?php

declare(strict_types=1);

it('does not register the password reset routes when the feature is off', function () {
    $router = app('router');

    expect($router->has('auth-kit.password.request'))->toBeFalse()
        ->and($router->has('auth-kit.password.email'))->toBeFalse()
        ->and($router->has('auth-kit.password.reset'))->toBeFalse()
        ->and($router->has('auth-kit.password.update'))->toBeFalse();
});
