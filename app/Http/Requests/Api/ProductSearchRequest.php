<?php

// [AI] Vygenerovane s pomocou AI (Claude) - validacia presunuta z ProductApiController.

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validacia vyhladavania produktov pre autocomplete (GET parameter q).
 */
class ProductSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
        ];
    }
}
