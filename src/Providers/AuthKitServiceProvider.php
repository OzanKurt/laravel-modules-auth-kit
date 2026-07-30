<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Providers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Kurt\Modules\AuthKit\AuthKitManager;
use Kurt\Modules\AuthKit\Contracts\Registrar;
use Kurt\Modules\AuthKit\Support\EloquentRegistrar;
use Kurt\Modules\Core\Contracts\UserResolver;
use Kurt\Modules\Core\Http\HttpMode;
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
            ->hasConfigFile('auth-kit')
            ->hasViews('auth-kit');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('auth-kit', fn ($app) => new AuthKitManager($app['config']));
        $this->app->alias('auth-kit', AuthKitManager::class);

        // The session guard is a StatefulGuard, but the contract has no default
        // binding; wire it so the auth actions can depend on the abstraction.
        $this->app->bind(StatefulGuard::class, fn ($app) => $app['auth']->guard());

        // Default user-creation seam for registration; rebindable by the host
        // app to control exactly how its User model is created.
        $this->app->singleton(Registrar::class, fn ($app) => new EloquentRegistrar(
            $app->make(UserResolver::class),
            $app->make(Hasher::class),
            $app['config'],
        ));
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        // Registered under the `auth-kit` namespace explicitly: spatie's
        // hasTranslations() would key off the (longer) package short-name.
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'auth-kit');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../lang' => $this->app->langPath('vendor/auth-kit'),
            ], 'auth-kit-translations');
        }

        $this->registerRoutesForMode();
    }

    /**
     * Register the module's HTTP surface based on its configured HttpMode.
     *
     * Boot-time and mode-driven: `ui` mounts the web routes, `api` mounts the
     * JSON routes under the `api` prefix, and `headless` registers nothing.
     */
    private function registerRoutesForMode(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        $mode = HttpMode::forModule('auth-kit');

        if ($mode === HttpMode::Ui) {
            Route::group([], fn () => require __DIR__.'/../../routes/web.php');
        }

        if ($mode === HttpMode::Api) {
            // M2 API mode is session-cookie based (token auth is a later
            // milestone), so the JSON routes need the stateful session stack
            // in addition to `api`. CSRF is intentionally omitted: JSON clients
            // authenticate via the session cookie, not a form token.
            Route::middleware([
                'api',
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
            ])->prefix('api')->group(fn () => require __DIR__.'/../../routes/api.php');
        }
    }
}
