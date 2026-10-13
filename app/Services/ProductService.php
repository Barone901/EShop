<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Sluzba pre pracu s produktami.
 */
class ProductService
{
    /**
     * Povolene sposoby radenia zoznamu produktov.
     */
    public const SORTS = ['newest', 'price_asc', 'price_desc', 'name'];

    /**
     * Ziska strankovany zoznam produktov s filtrom kategorie, vyhladavanim a radenim.
     */
    public function getProducts(
        ?string $search = null,
        ?string $categorySlug = null,
        ?string $sort = null,
        int $perPage = 12
    ): LengthAwarePaginator {
        $query = Product::query()->search($search);

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
