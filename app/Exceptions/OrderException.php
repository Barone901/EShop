<?php

// [AI] Vygenerovane s pomocou AI (Claude).
// [AI] Upravene s pomocou AI (Claude): odpoved vytvara render() (Laravel), controller nema try/catch.

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

/**
 * Ocakavana chyba pri spracovani objednavky (napr. malo kusov na sklade).
 * Sprava je urcena priamo pre zakaznika, preto nesmie obsahovat interne detaily.
 *
 * Laravel ju sam prevedie na odpoved cez render(): navrat na predchadzajucu stranku
 * s flash spravou "error" a povodnym vstupom formulara (checkout sa nemusi vypisat znova).
 */
class OrderException extends RuntimeException implements ShouldntReport
{
    public function render(): RedirectResponse
    {
        return back()
            ->withInput()
            ->with('error', $this->getMessage());
    }
}
