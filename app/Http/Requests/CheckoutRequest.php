<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\AddressType;
use App\Support\InputPattern;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

/**
 * Validacia checkout formulara (dorucovacia a fakturacna adresa, poznamka).
 */
class CheckoutRequest extends FormRequest
{
    /** Polia kontrolovane vzorom (rovnaky vzor ma aj formular v prehliadaci). */
    public const PATTERNS = [
        'shipping_name' => InputPattern::PERSON_NAME,
        'shipping_phone' => InputPattern::PHONE,
        'shipping_city' => InputPattern::CITY,
        'shipping_postal_code' => InputPattern::POSTAL_CODE,
        'billing_name' => InputPattern::PERSON_NAME,
        'billing_phone' => InputPattern::PHONE,
        'billing_city' => InputPattern::CITY,
        'billing_postal_code' => InputPattern::POSTAL_CODE,
        'billing_ico' => InputPattern::COMPANY_ID,
        'billing_dic' => InputPattern::COMPANY_ID,
        'billing_ic_dph' => InputPattern::COMPANY_ID,
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return InputPattern::applyTo([
            // Shipping
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_email' => ['required', 'email', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_postal_code' => ['required', 'string', 'max:10'],
            'shipping_country' => ['nullable', 'string', 'max:100'],

            // Billing
            'billing_name' => ['required', 'string', 'max:255'],
            'billing_email' => ['required', 'email', 'max:255'],
            'billing_phone' => ['nullable', 'string', 'max:20'],
            'billing_address' => ['required', 'string', 'max:255'],
            'billing_city' => ['required', 'string', 'max:100'],
            'billing_postal_code' => ['required', 'string', 'max:10'],
            'billing_country' => ['nullable', 'string', 'max:100'],

            'billing_company_name' => ['nullable', 'string', 'max:255'],
            'billing_ico' => ['nullable', 'string', 'max:20'],
            'billing_dic' => ['nullable', 'string', 'max:20'],
            'billing_ic_dph' => ['nullable', 'string', 'max:20'],

            // Other
            'note' => ['nullable', 'string', 'max:1000'],
        ], self::PATTERNS);
    }

    public function messages(): array
    {
        return InputPattern::messagesFor(self::PATTERNS);
    }

    /**
     * Citatelne nazvy poli pre standardne hlasky Laravelu (lang/en/validation.php),
     * napr. "The shipping name field is required."
     */
    public function attributes(): array
    {
        return [
            'shipping_name' => __('shipping name'),
            'shipping_email' => __('shipping email'),
            'shipping_phone' => __('shipping phone'),
            'shipping_address' => __('shipping address'),
            'shipping_city' => __('shipping city'),
            'shipping_postal_code' => __('shipping postal code'),
            'shipping_country' => __('shipping country'),
            'billing_name' => __('billing name'),
            'billing_email' => __('billing email'),
            'billing_phone' => __('billing phone'),
            'billing_address' => __('billing address'),
            'billing_city' => __('billing city'),
            'billing_postal_code' => __('billing postal code'),
            'billing_country' => __('billing country'),
            'billing_company_name' => __('company name'),
            'billing_ico' => __('company ID'),
            'billing_dic' => __('tax ID'),
            'billing_ic_dph' => __('VAT ID'),
        ];
    }

    /**
     * Overene data objednavky pripravene pre OrderService::createOrder().
     * Mapovanie poli formulara (shipping_*, billing_*) na stlpce adresy patri sem - model
     * ani sluzba nemusia poznat nazvy poli formulara.
     *
     * @return array{email: string, note: ?string, addresses: array<string, array<string, mixed>>}
     */
    public function orderData(): array
    {
        return [
            'email' => (string) $this->validated('shipping_email'),
            'note' => $this->validated('note'),
            'addresses' => collect(AddressType::cases())
                ->mapWithKeys(fn (AddressType $type) => [$type->value => $this->addressFor($type)])
                ->all(),
        ];
    }

    /**
     * Adresa daneho typu v tvare stlpcov tabulky order_addresses.
     *
     * @return array<string, mixed>
     */
    private function addressFor(AddressType $type): array
    {
        $field = fn (string $name) => $this->validated("{$type->value}_{$name}");

        // Formular ma jedno pole "meno", adresa ma meno a priezvisko zvlast
        [$firstName, $lastName] = Str::of((string) $field('name'))->squish()->explode(' ', 2)->pad(2, '')->all();

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => (string) $field('email'),
            'phone' => $field('phone'),
            'street' => (string) $field('address'),
            'city' => (string) $field('city'),
            'postal_code' => (string) $field('postal_code'),
            'country' => $field('country') ?? config('shop.default_country'),
            'company_name' => $field('company_name'),
            'ico' => $field('ico'),
            'dic' => $field('dic'),
            'ic_dph' => $field('ic_dph'),
        ];
    }
}
