<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Providers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Kurt\Modules\AuthKit\AuthKitManager;
use Kurt\Modules\Core\Providers\PackageServiceProvider;
use Spatie\LaravelPackageTools\Package;

final class AuthKitServiceProvider extends PackageServiceProvider
{
    protected function module(): string
    {
        return 'auth-kit';
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-modules-auth-kit')
            ->hasConfigFile('auth-kit');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('auth-kit', fn ($app) => new AuthKitManager($app['config']));
        $this->app->alias('auth-kit', AuthKitManager::class);

        // The session guard is a StatefulGuard, but the contract has no default
        // binding; wire it so the auth actions can depend on the abstraction.
        $this->app->bind(StatefulGuard::class, fn ($app) => $app['auth']->guard());
    }
}
