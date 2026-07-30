<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Creates a new user from validated registration data. The default binding is
 * {@see \Kurt\Modules\AuthKit\Support\EloquentRegistrar}; a host app can rebind
 * this to control exactly how its User model is created.
 */
interface Registrar
{
    /** @param  array<string, mixed>  $data  validated registration input */
    public function register(array $data): Authenticatable;
}
