<?php

// [AI] Upravene s pomocou AI (Claude): kategorie zo CategoryService, filtre, radenia a doprava
// sa posielaju do view (sablona necita request ani config).

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ProductSort;
use App\Http\Requests\ProductIndexRequest;
use App\Models\Product;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Support\Price;
use Illuminate\View\View;

/**
 * Controller pre zobrazenie a filtrovanie produktov.
 */
class ProductController extends Controller
{
    /**
     * Injektuje sluzby pre produkty a kategorie.
     */
    public function __construct(
        private readonly ProductService $productService,
        private readonly CategoryService $categoryService
    ) {}

    /**
     * Zobrazi zoznam produktov s filtrovanim, vyhladavanim a radenim.
     */
    public function index(ProductIndexRequest $request): View
    {
        // Len overene a vyplnene filtre - z nich view sklada odkazy (kategoria, radenie, zrusenie filtra)
        $filters = array_filter(
            $request->safe()->only(['category', 'search', 'sort']),
            fn ($value) => $value !== null && $value !== ''
        );

        $categories = $this->categoryService->getActiveCategories();

        return view('pages.products.index', [
            'products' => $this->productService->getProducts(
                $filters['search'] ?? null,
                $filters['category'] ?? null,
                $filters['sort'] ?? null,
            ),
            'categories' => $categories,
            'activeCategory' => $categories->firstWhere('slug', $filters['category'] ?? null),
            'filters' => $filters,
            'sorts' => ProductSort::cases(),
            'activeSort' => ProductSort::tryFrom($filters['sort'] ?? '') ?? ProductSort::Newest,
        ]);
    }

    /**
     * Zobrazi detail produktu s kategoriou.
     */
    public function show(Product $product): View
    {
        $product->load('category');

        // Produkty z neaktivnej kategorie nie su v ponuke (rovnako ako stranka kategorie)
        abort_unless($product->isVisible(), 404);

        return view('pages.products.show', [
            'product' => $product,
            'shippingCost' => Price::format((float) config('shop.shipping_cost')),
        ]);
    }
}
