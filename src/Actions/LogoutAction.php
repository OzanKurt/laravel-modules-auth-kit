<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Actions;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;

/**
 * Log the current user out and fully tear down the session: invalidate it and
 * regenerate the CSRF token so the old session cannot be replayed.
 */
final class LogoutAction
{
    public function __construct(
        private readonly StatefulGuard $guard,
        private readonly Request $request,
    ) {}

    public function handle(): void
    {
        $this->guard->logout();

        $this->request->session()->invalidate();
        $this->request->session()->regenerateToken();
    }
}
