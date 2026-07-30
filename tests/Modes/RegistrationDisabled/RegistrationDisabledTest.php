<?php

declare(strict_types=1);

it('does not register the registration routes when the feature is off', function () {
    expect(app('router')->has('auth-kit.register.attempt'))->toBeFalse();
});
