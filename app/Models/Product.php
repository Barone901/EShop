<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\MediaUrl;
use App\Support\Price;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model produktu v eshope.
 * Relacie: Category (N:1), OrderItem (1:N)
 *
 * [AI] Upravene s pomocou AI (Claude).
 */
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    // Do tohto poctu kusov sa produkt zobrazuje ako "Last items"
    public const LOW_STOCK_THRESHOLD = 5;

    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image_url',
        'category_id',
    ];

    /**
     * Pretypovanie atributov.
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    /**
     * Route model binding pouziva slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // =========================================================================
    // RELACIE
    // =========================================================================

    /**
     * Produkt patri do kategorie.
     * Relacia: N:1 (Viac produktov patri do jednej kategorie)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Produkt ma viacero poloziek objednavok (historicke referencie).
     * Relacia: 1:N (Jeden produkt je referencovany v mnohych polozkach objednavok)
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: Iba produkty na sklade.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope: Iba produkty viditelne v obchode (ich kategoria je aktivna).
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereHas('category', fn (Builder $q) => $q->active());
    }

    /**
     * Scope: Vyhladavanie podla nazvu alebo popisu.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        // % a _ su v LIKE zastupne znaky, v hladanom vyraze ich berieme doslovne
        $like = '%' . addcslashes($term, '\\%_') . '%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('name', 'like', $like)
                ->orWhere('description', 'like', $like);
        });
    }

    // =========================================================================
    // ACCESSORY
    // =========================================================================

    /**
     * Vrati formatovanu cenu s menou.
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::get(fn (): string => Price::format($this->price));
    }

    /**
     * Vrati verejnu URL obrazku (stlpec image_url obsahuje cestu alebo absolutnu URL).
     */
    protected function imageSrc(): Attribute
    {
        return Attribute::get(fn (): ?string => MediaUrl::from($this->image_url));
    }

    /**
     * Skontroluje ci je produkt na sklade.
     */
    protected function isInStock(): Attribute
    {
        return Attribute::get(fn (): bool => $this->stock > 0);
    }

    /**
     * Skontroluje ci zostava uz len par kusov.
     */
    protected function isLowStock(): Attribute
    {
        return Attribute::get(fn (): bool => $this->is_in_stock && $this->stock <= self::LOW_STOCK_THRESHOLD);
    }

    /**
     * Katalogove cislo produktu pre zakaznika, napr. "00042".
     */
    protected function catalogNumber(): Attribute
    {
        return Attribute::get(fn (): string => Str::padLeft((string) $this->id, 5, '0'));
    }

    /**
     * Ci je produkt viditelny v obchode (rovnaka podmienka ako scopeVisible).
     */
    public function isVisible(): bool
    {
        return (bool) $this->category?->is_active;
    }
}
