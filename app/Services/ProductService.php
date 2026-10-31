<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Sluzba pre pracu s produktami. Zobrazuju sa len produkty z aktivnych kategorii.
 */
class ProductService
{
    /**
     * Povolene sposoby radenia zoznamu produktov.
     */
    public const SORTS = ['newest', 'price_asc', 'price_desc', 'name'];

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

        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }
}
