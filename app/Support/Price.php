<?php

// [AI] Vygenerovane s pomocou AI (Claude).
// [AI] Upravene s pomocou AI (Claude): formatovanie robi Laravel (Number::currency), mena je v config/shop.php.

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Number;

/**
 * Jednotne formatovanie cien v eshope (napr. "€12.50").
 * Samotne formatovanie robi Laravel helper Number::currency (PHP intl); tato trieda len drzi
 * menu na jednom mieste, aby ju nebolo treba opakovat vo vsetkych modeloch a sluzbach.
 */
final class Price
{
    public static function format(float|int|string|null $amount): string
    {
        return Number::currency((float) $amount, in: config('shop.currency'));
    }
}
