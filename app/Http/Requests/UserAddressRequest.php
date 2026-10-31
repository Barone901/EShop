<?php

// [AI] Vygenerovane s pomocou AI (Claude) - validacia presunuta z controllera.

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\AddressType;
use App\Support\InputPattern;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validacia formulara adresy (vytvorenie aj uprava).
 * Typ adresy sa zadava len pri vytvoreni, pri uprave sa nemeni.
 */
class UserAddressRequest extends FormRequest
{
    /** Polia kontrolovane vzorom (rovnaky vzor ma aj formular v prehliadaci). */
    public const PATTERNS = [
        'first_name' => InputPattern::PERSON_NAME,
        'last_name' => InputPattern::PERSON_NAME,
        'phone' => InputPattern::PHONE,
        'city' => InputPattern::CITY,
        'postal_code' => InputPattern::POSTAL_CODE,
        'ico' => InputPattern::COMPANY_ID,
        'dic' => InputPattern::COMPANY_ID,
        'ic_dph' => InputPattern::COMPANY_ID,
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return InputPattern::applyTo([
            'type' => [
                Rule::requiredIf($this->isMethod('POST')),
                Rule::enum(AddressType::class),
            ],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'ico' => ['nullable', 'string', 'max:20'],
            'dic' => ['nullable', 'string', 'max:20'],
            'ic_dph' => ['nullable', 'string', 'max:20'],
            'is_default' => ['nullable', 'boolean'],
        ], self::PATTERNS);
    }

    public function messages(): array
    {
        return InputPattern::messagesFor(self::PATTERNS);
    }

    /**
     * Validovane data pripravene na ulozenie do modelu.
     */
    public function addressData(): array
    {
        $data = $this->safe()->except(['type', 'is_default']);
        $data['country'] ??= config('shop.default_country');

        return $data;
    }

    /**
     * Ci ma byt adresa nastavena ako predvolena.
     */
    public function wantsDefault(): bool
    {
        return $this->boolean('is_default');
    }
}
