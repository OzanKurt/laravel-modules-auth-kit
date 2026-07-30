<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Tests;

use Illuminate\Foundation\Application;

/**
 * Boots the package in `ui` mode so the web login/logout routes and views
 * register. The mode MUST be set before boot: route registration happens once,
 * during the provider's boot phase, so setting config mid-test is too late.
 */
abstract class UiModeTestCase extends TestCase
{
    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('auth-kit.http.mode', 'ui');
    }
}
