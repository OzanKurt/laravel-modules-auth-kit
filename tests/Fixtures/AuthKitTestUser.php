<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Tests\Fixtures;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Minimal Eloquent user for the package test suite. Extends the framework's
 * Authenticatable base so the session guard can authenticate it (password
 * hashing/verification and remember-token support come for free).
 *
 * Implements MustVerifyEmail (with the framework trait) so the M4 email
 * verification flow has a verifiable user: `email_verified_at`, the signed
 * verification URL, and `sendEmailVerificationNotification()` all work.
 */
final class AuthKitTestUser extends Authenticatable implements MustVerifyEmail
{
    use MustVerifyEmailTrait;
    use Notifiable;

    protected $table = 'users';

    protected $guarded = [];

    /**
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];
}
