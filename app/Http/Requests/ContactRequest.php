<?php

// [AI] Vygenerovane s pomocou AI (Claude) - validacia presunuta z controllera.

declare(strict_types=1);

namespace App\Http\Requests;

use App\Support\InputPattern;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validacia kontaktneho formulara.
 */
class ContactRequest extends FormRequest
{
    /** Polia kontrolovane vzorom (rovnaky vzor ma aj formular v prehliadaci). */
    public const PATTERNS = [
        'name' => InputPattern::PERSON_NAME,
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return InputPattern::applyTo([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], self::PATTERNS);
    }

    public function messages(): array
    {
        return InputPattern::messagesFor(self::PATTERNS);
    }
}
