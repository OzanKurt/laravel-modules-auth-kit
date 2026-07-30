<?php

declare(strict_types=1);

return [
    'http' => [
        // headless | api | ui  (resolved via Kurt\Modules\Core\Http\HttpMode)
        'mode' => 'ui',
    ],

    'login' => [
        'redirect_to' => '/',   // where a UI login redirects on success
        'allow_remember' => true,
    ],

    'register' => [
        'fields' => ['name', 'email'],  // assignable from registration input (never mass-assign the rest)
        'login_after' => true,          // log the new user in immediately
        'redirect_to' => '/',           // UI redirect on success
    ],

    'email_verification' => [
        'redirect_to' => '/',   // where a successful verification / already-verified lands
    ],

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

    // Each leaf is bool | Closure(\Illuminate\Database\Eloquent\Model $user): bool.
    // Nested (not flat dotted keys) so config() dot-notation resolves them,
    // e.g. config('auth-kit.gates.two_factor.enforced').
    'gates' => [
        'two_factor' => [
            'enforced' => false,
            'can_enable' => true,
        ],
        'otp_login' => [
            'can_use' => false,
        ],
        'registration' => [
            'can_register' => true,
        ],
    ],

    'lockout' => [
        'max_attempts' => 5,
        'decay_seconds' => 900,
    ],

    'passwordless' => [
        'token_ttl' => 900,
        'resend_cooldown' => 60,
    ],
];
