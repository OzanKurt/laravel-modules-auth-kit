<?php

declare(strict_types=1);

it('registers no auth routes in headless mode', function () {
    expect(app('router')->has('auth-kit.login'))->toBeFalse()
        ->and(app('router')->has('auth-kit.login.attempt'))->toBeFalse()
        ->and(app('router')->has('auth-kit.logout'))->toBeFalse();
});
