<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Support\InputPattern;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /** Polia kontrolovane vzorom (rovnaky vzor ma aj formular v prehliadaci). */
    public const PATTERNS = [
        'name' => InputPattern::USERNAME, // len písmená, medzery a pomlčky
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return InputPattern::applyTo([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:' . User::class,
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                // pravidla hesla su nastavene v AppServiceProvider (rovnake aj pri zmene hesla)
                Password::defaults(),
            ],
        ], self::PATTERNS);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...InputPattern::messagesFor(self::PATTERNS),
            'email.unique' => 'This email is already registered.',
        ];
    }
}
