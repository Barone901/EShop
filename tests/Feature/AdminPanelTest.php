<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        foreach (['categories', 'orders', 'products'] as $resource) {
            $this->get("/admin/{$resource}")->assertOk();
        }

        foreach (['categories', 'products'] as $resource) {
            $this->get("/admin/{$resource}/create")->assertOk();
        }
    }

    public function test_customer_cannot_access_admin(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $customer = tap(User::factory()->create())->assignRole('customer');

        $this->actingAs($customer)->get('/admin/products')->assertForbidden();
    }
}
