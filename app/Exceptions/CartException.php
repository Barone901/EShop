<?php

// [AI] Vygenerovane s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Ocakavana chyba pri praci s kosikom. Kod vynimky je HTTP status pre API odpoved.
 */
class CartException extends RuntimeException
{
    public static function notEnoughStock(): self
    {
        return new self('Not enough items in stock.', 422);
    }

    public static function quantityLimit(int $max): self
    {
        return new self("You can add at most {$max} pieces of one product.", 422);
    }

    public static function notInCart(): self
    {
        return new self('Product is not in cart.', 404);
    }

    public static function notAvailable(): self
    {
        return new self('Product is no longer available.', 404);
    }
}
