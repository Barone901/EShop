<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

declare(strict_types=1);

namespace App\Models;

use App\Support\Price;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model polozky objednavky so snapshotom produktovych dat.
 * Relacie: Order (N:1), Product (N:1)
 */
class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'subtotal',
    ];

    /**
     * Pretypovanie atributov.
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    // =========================================================================
    // RELACIE
    // =========================================================================

    /**
     * Polozka patri do objednavky.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Polozka referencuje produkt (data su ulozene ako snapshot).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // =========================================================================
    // ACCESSORY
    // =========================================================================

    /**
     * Vrati formatovanu jednotkovu cenu.
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::get(fn (): string => Price::format($this->price));
    }

    /**
     * Vrati formatovany medzisucet polozky.
     */
    protected function formattedSubtotal(): Attribute
    {
        return Attribute::get(fn (): string => Price::format($this->subtotal));
    }
}
