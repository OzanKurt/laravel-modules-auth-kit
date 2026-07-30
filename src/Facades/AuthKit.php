<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Facades;

use Illuminate\Support\Facades\Facade;
use Kurt\Modules\AuthKit\AuthKitManager;

/**
 * @method static string module()
 * @method static bool feature(string $name)
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
