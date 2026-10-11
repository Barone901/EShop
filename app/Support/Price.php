<?php

// [AI] Vygenerovane s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Support;

/**
 * Jednotne formatovanie cien v eshope (napr. "€12.50").
 */
final class Price
{
    public static function format(float|int|string|null $amount): string
    {
        return '€' . number_format((float) $amount, 2);
    }
}
