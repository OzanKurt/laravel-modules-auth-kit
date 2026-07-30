<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Minimal Eloquent user for the package test suite. Extends the framework's
 * Authenticatable base so the session guard can authenticate it (password
 * hashing/verification and remember-token support come for free).
 */
final class AuthKitTestUser extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];

    /**
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];
}
