<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Kurt\Modules\AuthKit\Actions\LoginAction;
use Kurt\Modules\AuthKit\Actions\LogoutAction;
use Kurt\Modules\AuthKit\Http\Requests\LoginRequest;
use Kurt\Modules\Core\Http\Controllers\ApiController;
use Kurt\Modules\Core\Http\HttpMode;

final class LoginController extends ApiController
{
    public function showForm(): View
    {
        return view('auth-kit::auth.login');
    }

    public function login(LoginRequest $request, LoginAction $action): RedirectResponse|JsonResponse
    {
        if (! $action->handle($request->credentials(), $request->remember())) {
            // Identical message for unknown-email and wrong-password (no enumeration).
            throw ValidationException::withMessages(['email' => trans('auth-kit::auth.failed')]);
        }

        if ($this->wantsJson($request)) {
            return $this->respond(['authenticated' => true]);
        }

        return redirect()->intended(config('auth-kit.login.redirect_to', '/'));
    }

    public function logout(Request $request, LogoutAction $action): RedirectResponse|JsonResponse
    {
        $action->handle();

        if ($this->wantsJson($request)) {
            return $this->respondNoContent();
        }

        return redirect('/');
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || HttpMode::forModule('auth-kit') === HttpMode::Api;
    }
}
