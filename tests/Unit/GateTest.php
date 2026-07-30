<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Kurt\Modules\AuthKit\Facades\AuthKit;

/** Minimal stand-in user for gate resolution. */
class GateUserStub extends Model
{
    protected $table = 'users';
    protected $guarded = [];
    public $timestamps = false;
}

it('resolves a boolean gate', function () {
    config()->set('auth-kit.gates.two_factor.enforced', true);
    expect(AuthKit::gate('two_factor.enforced', new GateUserStub))->toBeTrue();

    config()->set('auth-kit.gates.two_factor.enforced', false);
    expect(AuthKit::gate('two_factor.enforced', new GateUserStub))->toBeFalse();
});

it('resolves a closure gate against the given user', function () {
    config()->set('auth-kit.gates.two_factor.enforced', fn (Model $u) => $u->is_admin === true);

    expect(AuthKit::gate('two_factor.enforced', new GateUserStub(['is_admin' => true])))->toBeTrue();
    expect(AuthKit::gate('two_factor.enforced', new GateUserStub(['is_admin' => false])))->toBeFalse();
});

it('treats an unset gate as false', function () {
    expect(AuthKit::gate('missing.gate', new GateUserStub))->toBeFalse();
});
