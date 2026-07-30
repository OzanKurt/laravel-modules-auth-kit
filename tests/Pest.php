<?php

declare(strict_types=1);

use Kurt\Modules\AuthKit\Tests\ApiModeTestCase;
use Kurt\Modules\AuthKit\Tests\Fixtures\AuthKitTestUser;
use Kurt\Modules\AuthKit\Tests\HeadlessModeTestCase;
use Kurt\Modules\AuthKit\Tests\RegistrationDisabledModeTestCase;
use Kurt\Modules\AuthKit\Tests\TestCase;
use Kurt\Modules\AuthKit\Tests\UiModeTestCase;
use Kurt\Modules\AuthKit\Tests\VerificationDisabledModeTestCase;

// Route registration is boot-time and keyed off the module's HttpMode, so
// HTTP tests live under Modes/* where a mode-specific case fixes the mode
// before the app boots. Non-HTTP tests use the plain base case.
uses(TestCase::class)->in('Feature', 'Unit');
uses(UiModeTestCase::class)->in('Modes/Ui');
uses(ApiModeTestCase::class)->in('Modes/Api');
uses(HeadlessModeTestCase::class)->in('Modes/Headless');
uses(RegistrationDisabledModeTestCase::class)->in('Modes/RegistrationDisabled');
uses(VerificationDisabledModeTestCase::class)->in('Modes/VerificationDisabled');

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
