<?php


declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_name.required' => 'Shipping name is required.',
            'shipping_email.required' => 'Shipping email is required.',
            'shipping_email.email' => 'Please enter a valid email address.',
            'shipping_phone.required' => 'Shipping phone is required.',
            'shipping_address.required' => 'Shipping address is required.',
            'shipping_city.required' => 'Shipping city is required.',
            'shipping_postal_code.required' => 'Shipping postal code is required.',

            'billing_name.required' => 'Billing name is required.',
            'billing_email.required' => 'Billing email is required.',
            'billing_email.email' => 'Please enter a valid billing email address.',
            'billing_address.required' => 'Billing address is required.',
            'billing_city.required' => 'Billing city is required.',
            'billing_postal_code.required' => 'Billing postal code is required.',
        ];
    }
}
