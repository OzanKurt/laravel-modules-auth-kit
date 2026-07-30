<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Kurt\Modules\AuthKit\Concerns\InteractsWithAuthKit;
use Kurt\Modules\AuthKit\Contracts\AuthKitUser;

class TraitUserStub extends Model implements AuthKitUser
{
    use InteractsWithAuthKit;

    protected $table = 'users';
    protected $guarded = [];
    public $timestamps = false;
}

/** A user that overrides a default to prove override wins over config. */
class OverridingUserStub extends TraitUserStub
{
    public function isTwoFactorEnforced(): bool
    {
        return true;
    }
}

it('delegates capability methods to the matching config gate', function () {
    config()->set('auth-kit.gates.two_factor.can_enable', true);
    config()->set('auth-kit.gates.two_factor.enforced', false);
    config()->set('auth-kit.gates.otp_login.can_use', false);
    config()->set('auth-kit.gates.registration.can_register', true);

    $user = new TraitUserStub;

    expect($user->canEnableTwoFactor())->toBeTrue()
        ->and($user->isTwoFactorEnforced())->toBeFalse()
        ->and($user->canUseOtpLogin())->toBeFalse()
        ->and($user->canRegister())->toBeTrue();
});

it('resolves a closure gate with the user itself', function () {
    config()->set('auth-kit.gates.two_factor.enforced', fn (Model $u) => $u->getKey() === 7);

    $user = new TraitUserStub(['id' => 7]);
    expect($user->isTwoFactorEnforced())->toBeTrue();
});

it('lets a model override the trait default', function () {
    config()->set('auth-kit.gates.two_factor.enforced', false);
    expect((new OverridingUserStub)->isTwoFactorEnforced())->toBeTrue();
});
