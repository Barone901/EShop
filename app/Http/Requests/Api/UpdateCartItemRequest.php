<?php

// [AI] Vygenerovane s pomocou AI (Claude) - validacia presunuta z Api/CartController.

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validacia zmeny mnozstva produktu v kosiku (0 = odstranenie).
 */
class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0', 'max:' . config('shop.max_cart_quantity')],
        ];
    }
}
