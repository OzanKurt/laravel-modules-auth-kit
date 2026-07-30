<?php

declare(strict_types=1);

use Kurt\Modules\AuthKit\Tests\Fixtures\AuthKitTestUser;
use Kurt\Modules\AuthKit\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Persist a test user through the bound test case, so specs can write
 * `createUser([...])` without reaching for `$this`.
 *
 * @param  array<string, mixed>  $attributes
 */
function createUser(array $attributes = []): AuthKitTestUser
{
    /** @var TestCase $case */
    $case = test();

    return $case->createUser($attributes);
}
