<?php

// [AI] Vygenerovane s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Stav objednavky. HasLabel a HasColor pouziva Filament (selecty, badge v tabulke).
 */
enum OrderStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Farba badge vo Filamente.
     */
    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::Processing => 'primary',
            self::Shipped => 'purple',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    /**
     * Farba badge na frontende (komponent shop/order-status-badge).
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Confirmed => 'blue',
            self::Processing => 'indigo',
            self::Shipped => 'purple',
            self::Delivered => 'green',
            self::Cancelled => 'red',
        };
    }

    /**
     * Kroky postupu objednavky zobrazene zakaznikovi (bez "cancelled").
     *
     * @return list<self>
     */
    public static function progressSteps(): array
    {
        return [self::Pending, self::Confirmed, self::Processing, self::Shipped, self::Delivered];
    }

    /**
     * Zakaznik moze zrusit objednavku, kym sa nezacala spracovavat.
     */
    public function isCancellableByCustomer(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed], true);
    }

    /**
     * Admin moze zrusit objednavku, kym nebola odoslana.
     */
    public function isCancellableByAdmin(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed, self::Processing], true);
    }
}
