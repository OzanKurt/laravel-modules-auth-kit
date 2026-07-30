<?php

declare(strict_types=1);

it('serves the login form in ui mode', function () {
    $this->get(route('auth-kit.login'))
        ->assertOk()
        ->assertViewIs('auth-kit::auth.login');
});
