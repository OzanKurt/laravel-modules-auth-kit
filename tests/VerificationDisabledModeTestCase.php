<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Tests;

use Illuminate\Foundation\Application;

/**
 * Boots the package in `ui` mode with the `email_verification` feature turned
 * OFF, to prove the verification routes are absent when the flag is disabled.
 * Like the other mode cases, the config MUST be set before boot: route
 * registration happens once, during the provider's boot phase.
 */
abstract class VerificationDisabledModeTestCase extends TestCase
{
    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('auth-kit.http.mode', 'ui');
        $app['config']->set('auth-kit.features.email_verification', false);
    }
}
