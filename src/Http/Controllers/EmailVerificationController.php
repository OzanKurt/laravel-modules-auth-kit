<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Http\Controllers;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Kurt\Modules\Core\Http\Controllers\ApiController;
use Kurt\Modules\Core\Http\HttpMode;

final class EmailVerificationController extends ApiController
{
    public function notice(Request $request): View|JsonResponse|RedirectResponse
    {
        if ($this->verified($request)) {
            return $this->wantsJson($request)
                ? $this->respond(['verified' => true])
                : redirect()->intended(config('auth-kit.email_verification.redirect_to', '/'));
        }

        return $this->wantsJson($request)
            ? $this->respond(['verified' => false])
            : view('auth-kit::auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse|JsonResponse
    {
        // EmailVerificationRequest::authorize() already validated id + hash (403 otherwise).
        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $request->fulfill(); // marks verified + fires Verified
        }

        return $this->wantsJson($request)
            ? $this->respond(['verified' => true])
            : redirect()->intended(config('auth-kit.email_verification.redirect_to', '/'));
    }

    public function resend(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return $this->wantsJson($request)
            ? $this->respondNoContent()
            : back()->with('status', 'verification-link-sent');
    }

    private function verified(Request $request): bool
    {
        $user = $request->user();

        return ! $user instanceof MustVerifyEmail || $user->hasVerifiedEmail();
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || HttpMode::forModule('auth-kit') === HttpMode::Api;
    }
}
