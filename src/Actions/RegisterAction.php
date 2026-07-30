<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Actions;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Kurt\Modules\AuthKit\Contracts\Registrar;

/**
 * Register a new user: create them via the {@see Registrar}, fire the standard
 * Registered event (the app's default listener sends email verification when
 * the model implements MustVerifyEmail), then optionally log them in.
 */
final class RegisterAction
{
    public function __construct(
        private readonly Registrar $registrar,
        private readonly StatefulGuard $guard,
        private readonly Dispatcher $events,
        private readonly Repository $config,
    ) {}

    /** @param  array<string, mixed>  $data */
    public function handle(array $data): Authenticatable
    {
        $user = $this->registrar->register($data);

        $this->events->dispatch(new Registered($user));

        if ((bool) $this->config->get('auth-kit.register.login_after', true)) {
            $this->guard->login($user);
        }

        return $user;
    }
}
