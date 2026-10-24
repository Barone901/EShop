<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Testy hlavnych tokov eshopu: katalog, kosik, checkout, objednavky, adresy, clanky.
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

    private function addressData(array $attributes = []): array
    {
        return [
            'type' => 'shipping',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street' => 'Main Street 1',
            'city' => 'Martin',
            'postal_code' => '03601',
            ...$attributes,
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
        $this->getJson('/api/products/search?q=ham')->assertOk()->assertJsonPath('0.name', 'Hammer');
        $this->getJson('/api/products/hammer')->assertOk()->assertJsonPath('name', 'Hammer');
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

    public function test_user_can_view_and_cancel_order(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($user)->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
        $this->post('/orders', $this->checkoutData());
        $order = Order::firstOrFail();

        $this->get('/orders')->assertOk()->assertSee($order->order_number)->assertSee('1 item');
        $this->get('/dashboard')->assertOk()->assertSee($order->order_number);
        $this->get(route('orders.show', $order->order_number))
            ->assertOk()
            ->assertSee('Main Street 1')
            ->assertSee('Cancel Order');

        $this->post(route('orders.cancel', $order->order_number))
            ->assertSessionHas('success');

        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock);
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

    public function test_processing_order_cannot_be_cancelled(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'order_number' => 'ORD-TEST-0001',
            'user_id' => $user->id,
            'status' => Order::STATUS_PROCESSING,
            'subtotal' => 10,
            'shipping_cost' => 3,
            'total' => 13,
        ]);

        $this->actingAs($user)
            ->get(route('orders.show', $order->order_number))
            ->assertOk()
            ->assertDontSee('Cancel Order');

        $this->post(route('orders.cancel', $order->order_number))
            ->assertSessionHas('error');

        $this->assertSame(Order::STATUS_PROCESSING, $order->fresh()->status);
    }

    public function test_user_can_delete_only_own_cancelled_order(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($user)->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
        $this->post('/orders', $this->checkoutData());
        $order = Order::firstOrFail();

        // Aktivnu objednavku zmazat nejde
        $this->get(route('orders.show', $order->order_number))->assertDontSee('Delete Order');
        $this->delete(route('orders.destroy', $order->order_number))->assertSessionHas('error');
        $this->assertModelExists($order);

        $this->post(route('orders.cancel', $order->order_number));
        $this->get(route('orders.show', $order->order_number))->assertSee('Delete Order');

        // Cudzi pouzivatel zrusenu objednavku nezmaze
        $this->actingAs(User::factory()->create())
            ->delete(route('orders.destroy', $order->order_number))
            ->assertRedirect(route('orders.index'))
            ->assertSessionHas('error', 'Order not found.');
        $this->assertModelExists($order);

        // Vlastnik ju zmaze aj s polozkami a adresami
        $this->actingAs($user)
            ->delete(route('orders.destroy', $order->order_number))
            ->assertRedirect(route('orders.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($order);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseCount('order_addresses', 0);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_user_can_manage_addresses(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/addresses')->assertOk();
        $this->get('/addresses/create?type=billing')->assertOk()->assertSee('Company Details');

        $this->post('/addresses', $this->addressData(['is_default' => '1']))
            ->assertRedirect('/addresses');

        $address = UserAddress::firstOrFail();
        $this->assertSame('Slovakia', $address->country);
        $this->assertTrue($address->is_default);

        $this->get(route('addresses.edit', $address))->assertOk();
        // Formular na upravu neposiela "type" - typ adresy sa nemeni
        $this->put(route('addresses.update', $address), Arr::except($this->addressData(['city' => 'Zilina']), 'type'))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/addresses');
        $this->assertSame('Zilina', $address->fresh()->city);

        $second = UserAddress::create($this->addressData(['user_id' => $user->id, 'street' => 'Second 2']));
        $this->post(route('addresses.setDefault', $second))->assertRedirect('/addresses');
        $this->assertTrue($second->fresh()->is_default);

        $this->get('/checkout')->assertRedirect('/cart');

        $this->delete(route('addresses.destroy', $address))->assertRedirect('/addresses');
        $this->assertModelMissing($address);
    }

    public function test_default_addresses_are_prefilled_on_checkout_and_dashboard(): void
    {
        $user = User::factory()->create();
        UserAddress::create($this->addressData(['user_id' => $user->id, 'street' => 'Other Street 9']));
        UserAddress::create($this->addressData(['user_id' => $user->id, 'street' => 'Default Street 5', 'is_default' => true]));
        UserAddress::create($this->addressData(['user_id' => $user->id, 'type' => 'billing', 'company_name' => 'ACME s.r.o.']));

        $product = $this->createProduct();
        $this->actingAs($user)->postJson('/api/cart/add', ['product_id' => $product->id]);

        $this->get('/checkout')
            ->assertOk()
            ->assertSee('value="Default Street 5"', false)
            ->assertSee('ACME s.r.o.');

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Default Street 5')
            ->assertSee('ACME s.r.o.');
    }

    public function test_user_cannot_touch_foreign_address(): void
    {
        $owner = User::factory()->create();
        $address = UserAddress::create($this->addressData(['user_id' => $owner->id]));

        $this->actingAs(User::factory()->create());

        $this->get(route('addresses.edit', $address))->assertForbidden();
        $this->put(route('addresses.update', $address), $this->addressData())->assertForbidden();
        $this->post(route('addresses.setDefault', $address))->assertForbidden();
        $this->delete(route('addresses.destroy', $address))->assertForbidden();
    }

    public function test_article_content_is_sanitized(): void
    {
        Page::create([
            'title' => 'News',
            'slug' => 'news',
            'content' => '<h2>Heading</h2><script>alert("xss")</script>',
            'status' => Page::STATUS_PUBLISHED,
        ]);

        $this->get('/pages')->assertOk()->assertSee('News');
        $this->get('/pages/news')
            ->assertOk()
            ->assertSee('<h2>Heading</h2>', false)
            ->assertDontSee('alert("xss")', false);
    }
}
