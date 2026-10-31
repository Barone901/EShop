<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PageStatus;
use App\Enums\PaymentStatus;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\OrderService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresne testy k opravam v app/: sklad a ceny v kosiku, rusenie objednavok,
 * cisla objednavok, viditelnost produktov, slugy stranok, opravnenia admina, e-mail.
 */
class ShopIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(array $attributes = [], bool $activeCategory = true): Product
    {
        $category = Category::create([
            'name' => 'Tools',
            'slug' => 'tools-' . uniqid(),
            'is_active' => $activeCategory,
        ]);

        return Product::create([
            'name' => 'Hammer',
            'slug' => 'hammer-' . uniqid(),
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

    public function test_cart_total_quantity_cannot_exceed_stock(): void
    {
        $product = $this->createProduct(['stock' => 5]);

        $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 3])->assertOk();

        // 3 v kosiku + 3 nove = 6 > 5 na sklade
        $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 3])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Not enough items in stock.');

        $this->getJson('/api/cart')->assertJsonPath('count', 3);
    }

    public function test_cart_total_quantity_cannot_exceed_limit(): void
    {
        $product = $this->createProduct(['stock' => 500]);

        $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 99])->assertOk();

        $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 1])
            ->assertStatus(422)
            ->assertJsonPath('message', 'You can add at most 99 pieces of one product.');
    }

    public function test_cart_and_order_use_current_price(): void
    {
        $product = $this->createProduct();
        $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 2]);

        $product->update(['price' => 20]);

        $this->getJson('/api/cart')->assertJsonPath('formatted_total', '€40.00');

        $this->post('/orders', $this->checkoutData());

        $order = Order::with('items')->firstOrFail();
        $this->assertSame('40.00', $order->subtotal);
        $this->assertSame('43.00', $order->total);
        $this->assertEquals((float) $order->subtotal, $order->items->sum('subtotal'));
    }

    public function test_product_removed_from_shop_disappears_from_cart(): void
    {
        $product = $this->createProduct();
        $this->postJson('/api/cart/add', ['product_id' => $product->id]);

        $product->category->update(['is_active' => false]);

        $this->getJson('/api/cart')->assertJsonPath('count', 0);
        $this->get('/checkout')->assertRedirect('/cart');
    }

    public function test_order_numbers_are_unique_and_well_formed(): void
    {
        $numbers = collect(range(1, 50))->map(fn () => Order::generateOrderNumber());

        $this->assertCount(50, $numbers->unique());
        $numbers->each(fn (string $number) => $this->assertMatchesRegularExpression('/^ORD-\d{8}-[A-Z0-9]{6}$/', $number));
    }

    public function test_order_cannot_be_cancelled_twice(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct(['stock' => 5]);

        $this->actingAs($user)->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 2]);
        $this->post('/orders', $this->checkoutData());
        $order = Order::firstOrFail();
        $this->assertSame(3, $product->fresh()->stock);

        $this->post(route('orders.cancel', $order->order_number))->assertSessionHas('success');
        $this->post(route('orders.cancel', $order->order_number))
            ->assertSessionHas('error', 'Order is already cancelled.');

        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_admin_can_cancel_processing_order(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct(['stock' => 5]);

        $this->actingAs($user)->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 2]);
        $this->post('/orders', $this->checkoutData());
        $order = Order::firstOrFail();
        $order->update(['status' => OrderStatus::Processing]);

        app(OrderService::class)->cancelOrder($order, byAdmin: true);

        $this->assertSame(OrderStatus::Cancelled, $order->status);
        $this->assertSame(PaymentStatus::Failed, $order->payment_status);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_products_of_inactive_category_are_hidden(): void
    {
        $product = $this->createProduct(['name' => 'Secret drill'], activeCategory: false);

        $this->get('/products')->assertOk()->assertDontSee('Secret drill');
        $this->get(route('product.show', $product))->assertNotFound();
        $this->getJson('/api/products/search?q=secret')->assertOk()->assertExactJson([]);
        $this->getJson("/api/products/{$product->slug}")->assertNotFound();
        $this->postJson('/api/cart/add', ['product_id' => $product->id])->assertNotFound();
    }

    public function test_search_treats_wildcards_literally(): void
    {
        $this->createProduct(['name' => 'Hammer']);

        $this->get('/products?search=%25')->assertOk()->assertDontSee('Hammer');
        $this->getJson('/api/products/search?q=_%25')->assertOk()->assertExactJson([]);
    }

    public function test_page_slug_does_not_collide_with_trashed_page(): void
    {
        Page::create(['title' => 'News', 'status' => PageStatus::Published])->delete();

        $page = Page::create(['title' => 'News', 'status' => PageStatus::Published]);

        $this->assertSame('news-1', $page->slug);
    }

    public function test_article_view_is_counted_once_per_session(): void
    {
        $page = Page::create(['title' => 'News', 'status' => PageStatus::Published]);

        $this->get('/pages/news')->assertOk();
        $this->get('/pages/news')->assertOk();

        $this->assertSame(1, $page->fresh()->views_count);
    }

    public function test_banner_does_not_link_to_unpublished_page(): void
    {
        $page = Page::create(['title' => 'Draft', 'status' => PageStatus::Draft]);
        $banner = Banner::create(['name' => 'Promo', 'image_path' => 'promo.jpg', 'page_id' => $page->id]);

        $this->assertNull($banner->link);

        $page->update(['status' => PageStatus::Published]);

        $this->assertSame($page->url, $banner->fresh()->link);
    }

    public function test_admin_cannot_create_orders_or_touch_foreign_addresses(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = tap(User::factory()->create())->assignRole('admin');

        $address = UserAddress::create([
            'user_id' => User::factory()->create()->id,
            'type' => 'shipping',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street' => 'Main Street 1',
            'city' => 'Martin',
            'postal_code' => '03601',
        ]);

        $this->assertTrue($admin->can('viewAny', Order::class));
        $this->assertFalse($admin->can('create', Order::class));
        $this->assertFalse($admin->can('update', $address));
    }

    public function test_email_is_stored_lowercase_and_profile_accepts_uppercase(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        // Email musi mat domenu s platnym DNS zaznamom (pravidlo email:rfc,dns)
        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'Test.User@Gmail.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertSessionHasNoErrors();

        $user = User::firstOrFail();
        $this->assertSame('test.user@gmail.com', $user->email);

        $this->actingAs($user)
            ->patch('/profile', ['name' => 'Test User', 'email' => 'Test.User@Gmail.com'])
            ->assertSessionHasNoErrors();

        $this->assertSame('test.user@gmail.com', $user->fresh()->email);
    }
}
