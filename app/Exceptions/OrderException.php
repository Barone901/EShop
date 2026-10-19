<?php

// [AI] Vygenerovane s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Ocakavana chyba pri spracovani objednavky (napr. malo kusov na sklade).
 * Sprava je urcena priamo pre zakaznika, preto nesmie obsahovat interne detaily.
 */
class OrderException extends RuntimeException
{
}
