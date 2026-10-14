<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testy hlavnych tokov eshopu: katalog.
 */
class ShopTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(array $attributes = []): Product
    {
        $category = Category::create(['name' => 'Tools', 'slug' => 'tools']);

        return Product::create([
            'name' => 'Hammer',
            'slug' => 'hammer',
            'description' => 'Steel hammer',
            'price' => 10.50,
            'stock' => 5,
            'category_id' => $category->id,
            ...$attributes,
        ]);
    }

    public function test_catalog_pages_can_be_rendered(): void
    {
        $product = $this->createProduct();

        $this->get('/')->assertOk()->assertSee('Tools');
        $this->get('/products')->assertOk()->assertSee('Hammer');
        $this->get('/products?category=tools&sort=price_asc&search=ham')->assertOk()->assertSee('Hammer');
        $this->get(route('product.show', $product))->assertOk()->assertSee('Steel hammer');
        $this->get(route('category.show', 'tools'))->assertOk();
    }

    public function test_inactive_categories_are_hidden(): void
    {
        Category::create(['name' => 'Hidden category', 'slug' => 'hidden', 'is_active' => false]);

        $this->get('/')->assertOk()->assertDontSee('Hidden category');
        $this->get('/products')->assertOk()->assertDontSee('Hidden category');
        $this->get(route('category.show', 'hidden'))->assertNotFound();
    }
}
