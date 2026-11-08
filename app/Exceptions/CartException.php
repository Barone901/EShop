<?php

// [AI] Vygenerovane s pomocou AI (Claude).
// [AI] Upravene s pomocou AI (Claude): odpoved vytvara render() (Laravel), controller nema try/catch.

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\JsonResponse;
use RuntimeException;

/**
 * Ocakavana chyba pri praci s kosikom. Kod vynimky je HTTP status pre API odpoved.
 *
 * Laravel ju sam prevedie na odpoved cez render() - controller ju nezachytava.
 * ShouldntReport: je to odpoved pre pouzivatela, nie chyba aplikacie, preto sa nezapisuje do logu.
 */
class CartException extends RuntimeException implements ShouldntReport
{
    public static function notEnoughStock(): self
    {
        return new self(__('Not enough items in stock.'), 422);
    }

    public static function quantityLimit(int $max): self
    {
        return new self(__('You can add at most :max pieces of one product.', ['max' => $max]), 422);
    }

    public static function notInCart(): self
    {
        return new self(__('Product is not in cart.'), 404);
    }

    public static function notAvailable(): self
    {
        return new self(__('Product is no longer available.'), 404);
    }

    /**
     * JSON odpoved pre JavaScript (rovnaky tvar ako chyba validacie: pole "message").
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
