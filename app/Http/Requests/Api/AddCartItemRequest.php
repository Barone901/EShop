<?php

// [AI] Vygenerovane s pomocou AI (Claude) - validacia presunuta z Api/CartController.

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validacia pridania produktu do kosika.
 */
class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:' . config('shop.max_cart_quantity')],
        ];
    }
}
