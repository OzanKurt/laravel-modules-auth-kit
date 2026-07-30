<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Kurt\Modules\AuthKit\Actions\RegisterAction;
use Kurt\Modules\AuthKit\Http\Requests\RegisterRequest;
use Kurt\Modules\Core\Http\Controllers\ApiController;
use Kurt\Modules\Core\Http\HttpMode;

final class RegisterController extends ApiController
{
    public function showForm(): View
    {
        return view('auth-kit::auth.register');
    }

    public function register(RegisterRequest $request, RegisterAction $action): RedirectResponse|JsonResponse
    {
        $user = $action->handle($request->validated());

        if ($this->wantsJson($request)) {
            return $this->respondCreated(['registered' => true, 'id' => $user->getAuthIdentifier()]);
        }

        return redirect()->intended(config('auth-kit.register.redirect_to', '/'));
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || HttpMode::forModule('auth-kit') === HttpMode::Api;
    }
}
