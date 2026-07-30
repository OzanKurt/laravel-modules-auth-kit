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
}
