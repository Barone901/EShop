<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Controller pre vyhladavanie a zobrazenie produktov.
 */
class ProductApiController extends Controller
{
    /**
     * Injektuje sluzbu pre produkty.
     */
    public function __construct(
        private readonly ProductService $productService
    ) {}

    /**
     * Vyhlada produkty podla zadaneho vyrazu pre autocomplete.
     */
    public function search(Request $request): JsonResponse
    {
        // Server-side validacia search query
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $q = trim($validated['q'] ?? '');

        $products = $this->productService
            ->search($q)
            ->map(fn ($product) => $this->productService->formatForApi($product))
            ->values();

        return response()->json($products);
    }

    /**
     * Vrati detail produktu pre AJAX modal.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load('category');

        return response()->json(
            $this->productService->formatDetailForApi($product)
        );
    }
}
