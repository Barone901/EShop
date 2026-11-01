<?php

// [AI] Upravene s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductSearchRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
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
    public function search(ProductSearchRequest $request): AnonymousResourceCollection
    {
        return ProductResource::collection(
            $this->productService->search(trim((string) $request->validated('q')))
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
