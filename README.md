# Laravel Modules Auth Kit

[![tests](https://github.com/OzanKurt/laravel-modules-auth-kit/actions/workflows/tests.yml/badge.svg)](https://github.com/OzanKurt/laravel-modules-auth-kit/actions/workflows/tests.yml)

A maintained, orchestrating auth kit for KurtModules Laravel apps.

This is the alternative to the usual "install a scaffolding package, publish its
auth files, delete the package" dance. Nothing is scaffolded into the host app
and then abandoned: the controllers, routes and flows stay in the package and
keep receiving fixes. What you turn on is config, not copied code.

## Requirements

- PHP `^8.4`
- Laravel `^13.0`

## Installation

```bash
composer require ozankurt/laravel-modules-auth-kit
```

```bash
php artisan vendor:publish --tag=auth-kit-config
```

## HTTP modes

The `http.mode` setting decides what the package registers:

| Mode | Registers |
| --- | --- |
| `headless` | nothing - no routes, no views; you drive the actions yourself |
| `api` | JSON endpoints only |
| `ui` | Blade routes and views (default) |

Both surfaces run the same controllers, so an app can move from Blade to JSON
without the auth behaviour changing underneath it.

## Features

Each entry in `features` registers or withholds a whole flow, routes included:

```php
'features' => [
    'registration' => true,
    'email_verification' => true,
    'password_reset' => true,
    'password_confirmation' => true,
    'two_factor' => true,
    'otp_login' => false,
    'magic_link' => false,
    'sessions' => true,
    'login_journal' => true,
    'lockout' => true,
],
```

A disabled feature does not register its routes at all, so it cannot be reached
by URL guessing.

Passwordless login is opt-in in both of its forms: `otp_login` (a one-time code)
and `magic_link` (a signed link), both off by default.

## Per-user gates

Features answer "does this app have the flow at all?". Gates answer "may *this
user* use it?" - which is a different question, and the reason both exist.

```php
'gates' => [
    'two_factor' => [
        'enforced' => false,
        'can_enable' => true,
    ],
    'otp_login' => ['can_use' => false],
    'registration' => ['can_register' => true],
],
```

Every leaf is either a `bool` or a `Closure(Model $user): bool`, so a gate can
be a flat policy or a per-user decision without changing shape:

```php
'two_factor' => [
    'enforced' => fn ($user) => $user->is_admin,
],
```

Alternatively, implement `Kurt\Modules\AuthKit\Contracts\AuthKitUser` on the
user model and let the model answer for itself:

```php
interface AuthKitUser
{
    public function canEnableTwoFactor(): bool;
    public function isTwoFactorEnforced(): bool;
    public function canUseOtpLogin(): bool;
    public function canRegister(): bool;
}
```

Check either one through the facade:

```php
use Kurt\Modules\AuthKit\Facades\AuthKit;

AuthKit::feature('two_factor');                    // is the flow enabled at all?
AuthKit::gate('two_factor.enforced', $user);       // must THIS user use it?
```

## Registration

`registration.fields` is an allow-list of attributes assignable from
registration input. Anything outside it is never mass-assigned, so adding a
column to the users table cannot silently make it settable at signup.

To take over registration entirely, bind your own
`Kurt\Modules\AuthKit\Contracts\Registrar`.

## Lockout

On by default: `max_attempts` failures within `decay_seconds` locks the account
out. Leave it on unless you have another throttle in front of login.

```php
'lockout' => [
    'max_attempts' => 5,
    'decay_seconds' => 900,
],
```

## Testing

```bash
composer install
vendor/bin/pint --test
vendor/bin/phpstan analyse --memory-limit=2G
vendor/bin/pest
```

CI runs the same checks on every push and pull request
(`.github/workflows/tests.yml`), against PHP 8.4 / Laravel 13. Static analysis
is held at **PHPStan level 8**; the suite runs on **Pest 5**.

## License

MIT.
