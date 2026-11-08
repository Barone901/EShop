<?php

// [AI] Vygenerovane s pomocou AI (Claude) - validacia presunuta z ProductController.

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ProductSort;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validacia filtrov vypisu produktov (GET parametre category, search, sort).
 */
class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::enum(ProductSort::class)],
        ];
    }
}
