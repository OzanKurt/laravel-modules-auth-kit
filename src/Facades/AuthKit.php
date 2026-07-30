<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Facades;

use Illuminate\Support\Facades\Facade;
use Kurt\Modules\AuthKit\AuthKitManager;

/**
 * @method static string module()
 * @method static bool feature(string $name)
 * @method static bool gate(string $key, \Illuminate\Database\Eloquent\Model $user)
 *
 * @see AuthKitManager
 */
final class AuthKit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'auth-kit';
    }
}
