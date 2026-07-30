<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Kurt\Modules\AuthKit\Http\Requests\ForgotPasswordRequest;
use Kurt\Modules\Core\Http\Controllers\ApiController;
use Kurt\Modules\Core\Http\HttpMode;

final class PasswordResetController extends ApiController
{
    public function showLinkForm(): View
    {
        return view('auth-kit::auth.forgot-password');
    }

    /**
     * Always reports the same generic outcome, whether or not the address
     * belongs to an account: revealing the difference would let an attacker
     * enumerate registered emails.
     */
    public function sendLink(ForgotPasswordRequest $request): RedirectResponse|JsonResponse
    {
        Password::sendResetLink($request->only('email'));

        $status = trans('auth-kit::auth.password_reset_sent');

        return $this->wantsJson($request)
            ? $this->respond(['status' => $status])
            : back()->with('status', $status);
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || HttpMode::forModule('auth-kit') === HttpMode::Api;
    }
}
