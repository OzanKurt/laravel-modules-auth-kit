<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        // Deliberately NO `exists` rule: that would leak whether the account exists.
        return ['email' => ['required', 'string', 'email']];
    }
}
