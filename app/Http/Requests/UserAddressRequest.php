<?php

// [AI] Vygenerovane s pomocou AI (Claude) - validacia presunuta z controllera.

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\UserAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validacia formulara adresy (vytvorenie aj uprava).
 * Typ adresy sa zadava len pri vytvoreni, pri uprave sa nemeni.
 */
class UserAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => [
                Rule::requiredIf($this->isMethod('POST')),
                Rule::in(array_keys(UserAddress::TYPES)),
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
        ];
    }

    /**
     * Validovane data pripravene na ulozenie do modelu.
     */
    public function addressData(): array
    {
        $data = $this->safe()->except(['type', 'is_default']);
        $data['country'] ??= UserAddress::DEFAULT_COUNTRY;

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
