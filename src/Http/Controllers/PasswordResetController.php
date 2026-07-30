<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Kurt\Modules\AuthKit\Http\Requests\ForgotPasswordRequest;
use Kurt\Modules\AuthKit\Http\Requests\ResetPasswordRequest;
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

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth-kit::auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    /**
     * The broker verifies the (hashed, expiring) token itself; we only set the
     * new password and let it fire PasswordReset + invalidate the token.
     */
    public function reset(ResetPasswordRequest $request): RedirectResponse|JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            // The broker hands back whatever its user provider resolved; the
            // intersection spells out what this callback actually needs of it:
            // an Eloquent record to persist and an authenticatable to announce.
            function (Model&Authenticatable&CanResetPassword $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => trans($status)]);
        }

        $message = trans('auth-kit::auth.password_reset_success');

        return $this->wantsJson($request)
            ? $this->respond(['reset' => true, 'status' => $message])
            : redirect()->to((string) config('auth-kit.password_reset.redirect_to', '/'))->with('status', $message);
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || HttpMode::forModule('auth-kit') === HttpMode::Api;
    }
}
