<?php

// [AI] Vygenerovane s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Enums;

/**
 * Typ adresy - spolocny pre ulozene adresy pouzivatela aj adresy objednavky.
 */
enum AddressType: string
{
    case Shipping = 'shipping';
    case Billing = 'billing';

    public function label(): string
    {
        return match ($this) {
            self::Shipping => 'Shipping',
            self::Billing => 'Billing',
        };
    }
}
