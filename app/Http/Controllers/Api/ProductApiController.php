<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
    public function search(Request $request): AnonymousResourceCollection
    {
        // Server-side validacia search query
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        return ProductResource::collection(
            $this->productService->search(trim($validated['q'] ?? ''))
        );
    }

    /**
     * Vrati detail produktu pre AJAX modal.
     */
    public function show(Product $product): ProductDetailResource
    {
        $product->load('category');

        abort_unless($product->isVisible(), 404);

        return new ProductDetailResource($product);
    }
}
