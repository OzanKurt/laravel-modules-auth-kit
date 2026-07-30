<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Actions;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;

/**
 * Authenticate a set of credentials against the session guard. On success the
 * session id is regenerated (session-fixation defence). Returns whether the
 * attempt succeeded; the caller decides the response.
 */
final class LoginAction
{
    public function __construct(
        private readonly StatefulGuard $guard,
        private readonly Request $request,
    ) {}

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function handle(array $credentials, bool $remember = false): bool
    {
        if (! $this->guard->attempt($credentials, $remember)) {
            return false;
        }

        $this->request->session()->regenerate();

        return true;
    }
}
