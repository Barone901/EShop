<?php

// Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene.

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use App\Support\InputPattern;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /** Polia kontrolovane vzorom (rovnaky vzor ma aj formular v prehliadaci). */
    public const PATTERNS = [
        'first_name' => InputPattern::PERSON_NAME,
        'last_name' => InputPattern::PERSON_NAME,
        'name' => InputPattern::USERNAME,
        'phone' => InputPattern::PHONE,
    ];

    /**
     * E-mail normalizujeme na male pismena, aby pouzivatel nemusel rucne prepisovat
     * velke pismena (napr. ucet zaregistrovany pred zavedenim normalizacie).
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => mb_strtolower(trim($this->input('email')))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return InputPattern::applyTo([
            'first_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
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
            'email.unique' => 'This email is already used by another account.',
        ];
    }
}
