<?php

// [AI] Upravene s pomocou AI (Claude): odpoved obsahuje HTML z Blade komponentov namiesto API Resources.

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductSearchRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

/**
 * API Controller pre vyhladavanie a zobrazenie produktov.
 *
 * Vzhlad vysledkov je len v Blade komponentoch (View); JSON prenasa vyrenderovane HTML,
 * ktore JavaScript iba vlozi do stranky.
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
    public function search(ProductSearchRequest $request): JsonResponse
    {
        $products = $this->productService->search(trim((string) $request->validated('q')));

        return response()->json([
            'count' => $products->count(),
            'html' => view('components.shop.search-results', ['products' => $products])->render(),
        ]);
    }

    /**
     * Vrati detail produktu pre AJAX modal (quick view).
     */
    public function show(Product $product): JsonResponse
    {
        $product->load('category');

        abort_unless($product->isVisible(), 404);

        return response()->json([
            'html' => view('components.shop.quick-view', ['product' => $product])->render(),
        ]);
    }
}
