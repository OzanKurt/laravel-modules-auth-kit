<?php

declare(strict_types=1);

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kurt\Modules\AuthKit\Http\Controllers\EmailVerificationController;

it('does not error for a user that is not MustVerifyEmail (treated as verified)', function () {
    // A plain authenticatable-like user that does NOT implement MustVerifyEmail.
    $plainUser = new class
    {
        public function hasVerifiedEmail(): bool
        {
            return false;
        }
    };

    $request = Request::create('/verify-email', 'GET');
    $request->setUserResolver(fn () => $plainUser);

    $controller = new EmailVerificationController;

    // notice() must not throw and must short-circuit to a redirect (nothing to
    // verify) rather than attempting to render the notice or call verify APIs.
    expect($controller->notice($request))->toBeInstanceOf(RedirectResponse::class);
});
