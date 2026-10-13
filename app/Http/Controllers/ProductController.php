<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        private ProductService $productService
    ) {}

    /**
     * Zobrazi zoznam produktov s filtrovanim, vyhladavanim a radenim.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'category' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::in(ProductService::SORTS)],
        ]);

        $products = $this->productService->getProducts(
            $validated['search'] ?? null,
            $validated['category'] ?? null,
            $validated['sort'] ?? null,
        );

        $categories = Category::active()->orderBy('name')->get();

        return view('pages.products.index', compact('products', 'categories'));
    }

    /**
     * Zobrazi detail produktu s kategoriou.
     */
    public function show(Product $product): View
    {
        $product->load('category');

        return view('pages.products.show', compact('product'));
    }
}
