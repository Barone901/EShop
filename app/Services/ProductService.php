<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
     * Vyhlada produkty podla zadaneho vyrazu (autocomplete).
     */
    public function search(string $query, int $limit = 10): Collection
    {
        if (strlen($query) < 2) {
            return collect();
        }

        return Product::search($query)
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

    /**
     * Formatuje produkt pre API odpoved (vysledky vyhladavania).
     */
    public function formatForApi(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'formatted_price' => $product->formatted_price,
            'image_url' => $product->image_src,
            'category' => $product->category?->name,
            'in_stock' => $product->is_in_stock,
            'url' => route('product.show', $product),
        ];
    }

    /**
     * Formatuje produkt s detailmi pre API odpoved (quick view).
     */
    public function formatDetailForApi(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => $product->price,
            'formatted_price' => $product->formatted_price,
            'stock' => $product->stock,
            'in_stock' => $product->is_in_stock,
            'image_url' => $product->image_src,
            'category' => [
                'id' => $product->category?->id,
                'name' => $product->category?->name,
                'slug' => $product->category?->slug,
            ],
            'url' => route('product.show', $product),
        ];
    }
}
