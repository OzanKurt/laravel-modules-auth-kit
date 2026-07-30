<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Kurt\Modules\Core\Contracts\UserResolver;

final class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $table = app(UserResolver::class)->table();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique($table, 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
