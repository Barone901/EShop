<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PageStatus;
use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Model stranky/clanku s podporou soft delete.
 * Relacia: User (N:1)
 */
class Page extends Model
{
    use SoftDeletes;

    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'published_at',
        'author_id',
        'is_featured',
        'sort_order',
        'views_count',
    ];

    /**
     * Pretypovanie atributov.
     */
    protected function casts(): array
    {
        return [
            'status' => PageStatus::class,
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'views_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Automaticke generovanie a unikatnost slugu pri ukladani.
     */
    protected static function booted(): void
    {
        static::saving(function (self $page) {
            if (!$page->exists && empty($page->slug)) {
                $page->slug = Str::slug((string) $page->title);
            }

            if ($page->exists && $page->isDirty('title') && !$page->isDirty('slug')) {
                $page->slug = Str::slug((string) $page->title);
            }

            if (!empty($page->slug)) {
                $page->slug = static::makeUniqueSlug($page, (string) $page->slug);
            }
        });
    }

    /**
     * Vytvori unikatny slug pridanim ciselneho suffixu.
     * Kontroluje aj zmazane (soft delete) stranky - unikatny index v DB ich obsahuje tiez.
     */
    private static function makeUniqueSlug(self $page, string $slug): string
    {
        $base = $slug;
        $i = 1;

        while (static::slugIsTaken($page, $slug)) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    private static function slugIsTaken(self $page, string $slug): bool
    {
        return static::withTrashed()
            ->where('slug', $slug)
            ->when($page->exists, fn (Builder $query) => $query->whereKeyNot($page->getKey()))
            ->exists();
    }

    // =========================================================================
    // RELACIE
    // =========================================================================

    /**
     * Stranka patri autorovi.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: Iba publikovane stranky s platnym published_at.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PageStatus::Published)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Ci je stranka verejne dostupna (rovnaka podmienka ako scopePublished).
     */
    public function isPublished(): bool
    {
        return !$this->trashed()
            && $this->status === PageStatus::Published
            && ($this->published_at === null || $this->published_at->lte(now()));
    }

    /**
     * Scope: Iba zvyraznene stranky.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Zoradenie podla sort_order a published_at.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at');
    }

    // =========================================================================
    // ACCESSORY
    // =========================================================================

    /**
     * Vrati URL hlavneho obrazku stranky.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        return MediaUrl::from($this->featured_image);
    }

    /**
     * Vrati HTML obsah bez nebezpecnych prvkov (script, on* atributy...).
     * Obsah pise redaktor v admine, preto ho pred vypisom cez {!! !!} sanitizujeme.
     */
    public function getSafeContentAttribute(): string
    {
        return Str::sanitizeHtml((string) $this->content);
    }

    /**
     * Zvysi pocitadlo zobrazeni o 1.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Vrati URL stranky.
     */
    public function getUrlAttribute(): string
    {
        return route('pages.show', (string) $this->slug);
    }
}
