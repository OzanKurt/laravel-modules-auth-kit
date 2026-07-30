<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Contracts;

use Kurt\Modules\AuthKit\Concerns\InteractsWithAuthKit;

/**
 * Per-user auth capabilities. A host User model implements this (usually via
 * {@see InteractsWithAuthKit}) so auth-kit can
 * ask "is this allowed for this user?" independently of whether the feature is
 * enabled app-wide.
 */
interface AuthKitUser
{
    public function canEnableTwoFactor(): bool;

    public function isTwoFactorEnforced(): bool;

    public function canUseOtpLogin(): bool;

    public function canRegister(): bool;
}
