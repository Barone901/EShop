<?php

// [AI] Vygenerovane s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Sposob platby objednavky.
 */
enum PaymentMethod: string implements HasLabel
{
    case CashOnDelivery = 'cod';

    public function getLabel(): string
    {
        return match ($this) {
            self::CashOnDelivery => 'Cash on Delivery',
        };
    }
}
