<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

declare(strict_types=1);

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model bannera pre homepage slider.
 * Relacia: Page (N:1)
 */
class Banner extends Model
{
    /** @use HasFactory<\Database\Factories\BannerFactory> */
    use HasFactory;

    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'name',
        'image_path',
        'link_url',
        'page_id',
        'is_active',
        'sort_order',
    ];

    /**
     * Pretypovanie atributov.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Banner moze ukazovat na konkretnu stranku.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Vrati finalny odkaz bannera (link_url alebo route na stranku).
     */
    protected function link(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (!empty($this->link_url)) {
                return (string) $this->link_url;
            }

            // Na nepublikovanu stranku neodkazujeme (skoncilo by to 404)
            if ($this->page?->isPublished()) {
                return $this->page->url;
            }

            return null;
        });
    }

    /**
     * Scope: Iba aktivne bannery.
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

    /**
     * Vrati URL obrazku bannera.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => MediaUrl::from($this->image_path));
    }
}
