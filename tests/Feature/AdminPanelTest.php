<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PageStatus;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Models\Order;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Zakladne testy administracie (Filament).
 */
class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        return tap(User::factory()->create())->assignRole('admin');
    }

    public function test_admin_can_open_resource_pages(): void
    {
        $this->actingAs($this->admin());

        foreach (['banners', 'categories', 'orders', 'pages', 'products'] as $resource) {
            $this->get("/admin/{$resource}")->assertOk();
        }

        foreach (['banners', 'categories', 'pages', 'products'] as $resource) {
            $this->get("/admin/{$resource}/create")->assertOk();
        }
    }

    public function test_customer_cannot_access_admin(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $customer = tap(User::factory()->create())->assignRole('customer');

        $this->actingAs($customer)->get('/admin/products')->assertForbidden();
    }

    public function test_designer_can_bulk_delete_pages(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $designer = tap(User::factory()->create())->assignRole('designer');

        $this->assertTrue($designer->can('deleteAny', Page::class));
        $this->assertTrue($designer->can('restoreAny', Page::class));
    }

    public function test_page_can_be_created_in_admin(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'New article',
                'slug' => 'new-article',
                'status' => PageStatus::Published->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('pages', ['slug' => 'new-article']);
    }

    public function test_admin_changes_order_status_only_through_actions(): void
    {
        $this->actingAs($this->admin());
        $order = Order::factory()->create();

        $this->get("/admin/orders/{$order->getRouteKey()}/edit")->assertOk();

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->assertFormFieldIsDisabled('status')
            ->assertFormFieldIsDisabled('payment_status')
            ->assertActionHidden('deliver')
            ->callAction('confirm')
            ->assertHasNoActionErrors();

        $this->assertSame(OrderStatus::Confirmed, $order->fresh()->status);
    }
}
