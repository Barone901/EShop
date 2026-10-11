<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model kategorie produktov s podporou hierarchie.
 * Relacie: Category (N:1 self), Category (1:N self), Product (1:N)
 */
class Category extends Model
{
    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image_url',
        'is_active',
        'sort_order',
    ];

    /**
     * Pretypovanie atributov.
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
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
     * Kategoria patri do rodricovskej kategorie.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Kategoria ma viacero detskych kategorii.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Kategoria ma viacero produktov.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->latest();
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: Iba hlavne kategorie (bez parent_id).
     */
    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Iba aktivne kategorie.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Zoradenie podla sort_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    // =========================================================================
    // ACCESSORY
    // =========================================================================

    /**
     * Vrati verejnu URL obrazku (stlpec image_url obsahuje cestu alebo absolutnu URL).
     */
    public function getImageSrcAttribute(): ?string
    {
        return MediaUrl::from($this->image_url);
    }
}
