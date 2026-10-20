<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testy hlavnych tokov eshopu: katalog, kosik, checkout.
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

    private function checkoutData(): array
    {
        return [
            'shipping_name' => 'John Doe',
            'shipping_email' => 'john@gmail.com',
            'shipping_phone' => '+421900000000',
            'shipping_address' => 'Main Street 1',
            'shipping_city' => 'Martin',
            'shipping_postal_code' => '03601',
            'billing_name' => 'John Doe',
            'billing_email' => 'john@gmail.com',
            'billing_address' => 'Main Street 1',
            'billing_city' => 'Martin',
            'billing_postal_code' => '03601',
        ];
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

    public function test_cart_includes_shipping_cost(): void
    {
        $product = $this->createProduct();

        $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 2])
            ->assertOk()
            ->assertJsonPath('cart.count', 2)
            ->assertJsonPath('cart.formatted_total', '€21.00')
            ->assertJsonPath('cart.formatted_shipping_cost', '€3.00')
            ->assertJsonPath('cart.formatted_grand_total', '€24.00');
    }

    public function test_updating_product_not_in_cart_returns_404(): void
    {
        $this->postJson('/api/cart/update', ['product_id' => 999, 'quantity' => 1])
            ->assertNotFound();
    }

    public function test_guest_can_place_order_and_see_confirmation(): void
    {
        $product = $this->createProduct();
        $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 2]);

        $this->get('/checkout')->assertOk()->assertSee('€24.00');

        $response = $this->post('/orders', $this->checkoutData());

        $order = Order::firstOrFail();
        $response->assertRedirect(route('orders.confirmation', $order->order_number));

        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame('24.00', $order->total);

        $this->get(route('orders.confirmation', $order->order_number))
            ->assertOk()
            ->assertSee('Main Street 1')
            ->assertSee('john@gmail.com')
            ->assertDontSee('My Orders');
    }

    public function test_order_fails_gracefully_when_stock_is_insufficient(): void
    {
        $product = $this->createProduct();
        $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 2]);
        $product->update(['stock' => 1]);

        $this->post('/orders', $this->checkoutData())
            ->assertSessionHas('error', 'Not enough stock for "Hammer". Available: 1.');

        $this->assertSame(0, Order::count());
    }

    public function test_guest_cannot_view_confirmation_of_foreign_order(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-0002',
            'user_id' => User::factory()->create()->id,
            'subtotal' => 10,
            'shipping_cost' => 3,
            'total' => 13,
        ]);

        $this->get(route('orders.confirmation', $order->order_number))
            ->assertRedirect(route('home'))
            ->assertSessionHas('error', 'Order not found.');
    }
}
