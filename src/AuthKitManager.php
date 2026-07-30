<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;

final class AuthKitManager
{
    public function __construct(private readonly Repository $config) {}

    /** Module slug used for config lookups. */
    public function module(): string
    {
        return 'auth-kit';
    }

    /**
     * Whether an auth-kit flow is enabled in the host app.
     *
     * Unknown flags resolve to false (safe-by-default: a flow the app never
     * declared is off).
     */
    public function feature(string $name): bool
    {
        return (bool) $this->config->get("auth-kit.features.{$name}", false);
    }

    /**
     * Resolve a per-user capability gate.
     *
     * The config value at "auth-kit.gates.{$key}" is either a bool (static
     * answer) or a Closure(Model $user): bool (dynamic answer). Anything else,
     * or an unset key, resolves to false.
     */
    public function gate(string $key, Model $user): bool
    {
        $value = $this->config->get("auth-kit.gates.{$key}", false);

        if ($value instanceof Closure) {
            return (bool) $value($user);
        }

        return (bool) $value;
    }
}
