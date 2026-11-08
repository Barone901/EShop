<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

declare(strict_types=1);

namespace App\Services;

use App\Enums\ProductSort;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Sluzba pre pracu s produktami. Zobrazuju sa len produkty z aktivnych kategorii.
 */
class ProductService
{
    /**
     * Minimalna dlzka vyrazu pre autocomplete (rovnaka ako v search.js).
     */
    private const MIN_SEARCH_LENGTH = 2;

    /**
     * Vyhlada produkty podla zadaneho vyrazu (autocomplete).
     */
    public function search(string $query, int $limit = 10): Collection
    {
        if (mb_strlen($query) < self::MIN_SEARCH_LENGTH) {
            return collect();
        }

        return Product::visible()
            ->search($query)
            ->with('category')
            ->limit($limit)
            ->get();
    }

    /**
     * Ziska strankovany zoznam produktov s filtrom kategorie, vyhladavanim a radenim.
     */
    public function getProducts(
        ?string $search = null,
        ?string $categorySlug = null,
        ?string $sort = null,
        int $perPage = 12
    ): LengthAwarePaginator {
        $query = Product::visible()->search($search);

        if ($categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        match (ProductSort::tryFrom((string) $sort)) {
            ProductSort::PriceAsc => $query->orderBy('price'),
            ProductSort::PriceDesc => $query->orderByDesc('price'),
            ProductSort::Name => $query->orderBy('name'),
            default => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }
}
