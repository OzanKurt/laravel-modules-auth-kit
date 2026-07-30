<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit;

use Illuminate\Contracts\Config\Repository;

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
}
