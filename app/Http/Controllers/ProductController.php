<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductIndexRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\View\View;

/**
 * Controller pre zobrazenie a filtrovanie produktov.
 */
class ProductController extends Controller
{
    /**
     * Injektuje sluzbu pre produkty.
     */
    public function __construct(
        private readonly ProductService $productService
    ) {}

    /**
     * Zobrazi zoznam produktov s filtrovanim, vyhladavanim a radenim.
     */
    public function index(ProductIndexRequest $request): View
    {
        return view('pages.products.index', [
            'products' => $this->productService->getProducts(
                $request->validated('search'),
                $request->validated('category'),
                $request->validated('sort'),
            ),
            'categories' => Category::active()->orderBy('name')->get(),
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
        ]);
    }
}
