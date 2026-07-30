<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Kurt\Modules\AuthKit\Providers\AuthKitServiceProvider;
use Kurt\Modules\AuthKit\Tests\Fixtures\AuthKitTestUser;
use Kurt\Modules\Core\Testing\PackageTestCase;

abstract class TestCase extends PackageTestCase
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function modulePackageProviders($app): array
    {
        return [AuthKitServiceProvider::class];
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        // Authenticate against the package's test user, using an in-memory
        // session so the guard's session/token operations work under test.
        $app['config']->set('auth.providers.users.model', AuthKitTestUser::class);
        $app['config']->set('session.driver', 'array');

        // The web/stateful stacks encrypt the session cookie, which needs a key.
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Outside an HTTP request there is no StartSession middleware, so the
        // container's request has no session store. Attach a started one so
        // actions invoked directly (unit tests) can regenerate/invalidate it;
        // HTTP feature tests get their own session from the middleware stack.
        $store = $this->app['session']->driver();
        $store->start();
        $this->app['request']->setLaravelSession($store);
    }

    protected function defineDatabaseMigrations(): void
    {
        parent::defineDatabaseMigrations();

        // The shared users table lacks auth columns; add them for this package.
        Schema::table('users', function (Blueprint $table): void {
            $table->string('password')->nullable();
            $table->rememberToken();
            // Email verification (M4): unverified users have this null.
            $table->timestamp('email_verified_at')->nullable();
        });

        // Password reset (M5) runs on Laravel's own broker, which stores its
        // (hashed, expiring) tokens here. The framework owns this table, so the
        // package ships no migration for it: only the test harness creates it.
        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Create a persisted test user. Passwords are hashed unless already given
     * hashed, so `Auth::attempt` can verify them.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function createUser(array $attributes = []): AuthKitTestUser
    {
        $attributes = array_merge([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('secret12'),
        ], $attributes);

        return AuthKitTestUser::query()->create($attributes);
    }
}
