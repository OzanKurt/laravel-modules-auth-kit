<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Concerns;

use Kurt\Modules\AuthKit\Facades\AuthKit;

/**
 * Config-backed default implementations of {@see \Kurt\Modules\AuthKit\Contracts\AuthKitUser}.
 *
 * Each method delegates to the matching gate in `config('auth-kit.gates.*')`.
 * A host model may override any method for full per-user control; the override
 * wins because it shadows the trait method.
 */
trait InteractsWithAuthKit
{
    public function canEnableTwoFactor(): bool
    {
        return AuthKit::gate('two_factor.can_enable', $this);
    }

    public function isTwoFactorEnforced(): bool
    {
        return AuthKit::gate('two_factor.enforced', $this);
    }

    public function canUseOtpLogin(): bool
    {
        return AuthKit::gate('otp_login.can_use', $this);
    }

    public function canRegister(): bool
    {
        return AuthKit::gate('registration.can_register', $this);
    }
}
