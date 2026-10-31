<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Support\Price;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * Model objednavky.
 * Relacie: User (N:1), OrderItem (1:N), OrderAddress (1:N)
 *
 * [AI] Upravene s pomocou AI (Claude): enumy pre stavy, unikatne cislo objednavky.
 */
class Order extends Model
{
    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'order_number',
        'user_id',
        'guest_token',
        'guest_email',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'shipping_cost',
        'total',
        'note',
    ];

    /**
     * Polia skryte pri serializacii.
     */
    protected $hidden = [
        'guest_token',
    ];

    /**
     * Pretypovanie atributov.
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_method' => PaymentMethod::class,
            'payment_status' => PaymentStatus::class,
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Vygeneruje unikatne cislo objednavky, napr. ORD-20261031-K7Q2XM.
     */
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Skontroluje ci moze zakaznik objednavku zrusit.
     */
    public function isCancellable(): bool
    {
        return $this->status->isCancellableByCustomer();
    }

    /**
     * Skontroluje ci je objednavka zrusena.
     */
    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::Cancelled;
    }

    /**
     * Skontroluje ci moze zakaznik objednavku zmazat zo svojho uctu.
     * Len zrusenu - tovar uz bol vrateny na sklad a objednavka sa nebude vybavovat.
     */
    public function isDeletable(): bool
    {
        return $this->isCancelled();
    }

    /**
     * Vrati index aktualneho kroku v OrderStatus::progressSteps() (-1 pre zrusenu objednavku).
     */
    public function progressStep(): int
    {
        $index = array_search($this->status, OrderStatus::progressSteps(), true);

        return $index === false ? -1 : $index;
    }

    // =========================================================================
    // RELACIE
    // =========================================================================

    /**
     * Objednavka patri pouzivatelovi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Objednavka ma viacero poloziek.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Objednavka ma viacero adries.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    /**
     * Vrati dorucovaciu adresu objednavky.
     */
    public function shippingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)
            ->where('type', AddressType::Shipping);
    }

    /**
     * Vrati fakturacnu adresu objednavky.
     */
    public function billingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)
            ->where('type', AddressType::Billing);
    }

    // =========================================================================
    // ACCESSORY
    // =========================================================================

    /**
     * Vrati formatovanu celkovu sumu.
     */
    public function getFormattedTotalAttribute(): string
    {
        return Price::format($this->total);
    }

    /**
     * Vrati formatovanu cenu dopravy.
     */
    public function getFormattedShippingCostAttribute(): string
    {
        return Price::format($this->shipping_cost);
    }

    /**
     * Vrati formatovany medzisucet.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return Price::format($this->subtotal);
    }
}
