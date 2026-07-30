<?php

declare(strict_types=1);

it('does not register the verification routes when the feature is off', function () {
    $router = app('router');

    expect($router->has('auth-kit.verification.notice'))->toBeFalse()
        ->and($router->has('auth-kit.verification.verify'))->toBeFalse()
        ->and($router->has('auth-kit.verification.send'))->toBeFalse();
});
