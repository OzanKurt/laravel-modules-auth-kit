<?php

declare(strict_types=1);

it('registers via the API with a JSON envelope', function () {
    $this->postJson(route('auth-kit.register.attempt'), [
        'name' => 'Ada', 'email' => 'ada@b.com',
        'password' => 'secret12', 'password_confirmation' => 'secret12',
    ])->assertCreated()->assertJsonPath('data.registered', true);
});
