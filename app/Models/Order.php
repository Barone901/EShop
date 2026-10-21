<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Price;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model objednavky.
 * Relacie: User (N:1), OrderItem (1:N), OrderAddress (1:N)
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
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    // Cena dopravy (dobierka) v EUR
    public const SHIPPING_COST = 3.00;

    // Konstanty pre statusy objednavky
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_SHIPPED => 'Shipped',
        self::STATUS_DELIVERED => 'Delivered',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    // Kroky postupu objednavky zobrazene zakaznikovi (bez "cancelled")
    public const PROGRESS_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_PROCESSING,
        self::STATUS_SHIPPED,
        self::STATUS_DELIVERED,
    ];

    // Statusy, v ktorych moze zakaznik objednavku zrusit
    public const CANCELLABLE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
    ];

    // Konstanty pre platobne statusy
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_PENDING => 'Pending',
        self::PAYMENT_PAID => 'Paid',
        self::PAYMENT_FAILED => 'Failed',
    ];

    // Konstanty pre platobne metody
    public const PAYMENT_COD = 'cod';

    public const PAYMENT_METHODS = [
        self::PAYMENT_COD => 'Cash on Delivery',
    ];

    /**
     * Vygeneruje unikatne cislo objednavky.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid('', true), -4));

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Skontroluje ci moze zakaznik objednavku zrusit.
     */
    public function isCancellable(): bool
    {
        return in_array($this->status, self::CANCELLABLE_STATUSES, true);
    }

    /**
     * Skontroluje ci je objednavka zrusena.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
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
     * Vrati index aktualneho kroku v PROGRESS_STATUSES (-1 pre zrusenu objednavku).
     */
    public function progressStep(): int
    {
        $index = array_search($this->status, self::PROGRESS_STATUSES, true);

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
            ->where('type', OrderAddress::TYPE_SHIPPING);
    }

    /**
     * Vrati fakturacnu adresu objednavky.
     */
    public function billingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)
            ->where('type', OrderAddress::TYPE_BILLING);
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

    /**
     * Vrati textovu reprezentaciu statusu.
     */
    public function getStatusTextAttribute(): string
    {
        return self::STATUSES[$this->status] ?? (string) $this->status;
    }

    /**
     * Vrati textovu reprezentaciu platobneho statusu.
     */
    public function getPaymentStatusTextAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? (string) $this->payment_status;
    }

    /**
     * Vrati textovu reprezentaciu platobnej metody.
     */
    public function getPaymentMethodTextAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? (string) $this->payment_method;
    }

    /**
     * Vrati farbu statusu pre UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ((string) $this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_PROCESSING => 'indigo',
            self::STATUS_SHIPPED => 'purple',
            self::STATUS_DELIVERED => 'green',
            self::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }
}
