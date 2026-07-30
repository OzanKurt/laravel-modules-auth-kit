<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Tests;

use Illuminate\Foundation\Application;

/**
 * Boots the package in `api` mode so the JSON login/logout routes register
 * (under the `api` prefix). The mode is set before boot for the same reason as
 * {@see UiModeTestCase}: routes are wired once, during the provider boot phase.
 */
abstract class ApiModeTestCase extends TestCase
{
    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('auth-kit.http.mode', 'api');
    }
}
