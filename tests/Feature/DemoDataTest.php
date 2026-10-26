<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Ukazkove data, ktore sa importuju pri prvom spusteni v Dockeri.
 */
class DemoDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_working_demo_shop(): void
    {
        Storage::fake('public');

        $this->seed();

        foreach (['admin@admin.com' => 'admin', 'employee@example.com' => 'employee', 'designer@example.com' => 'designer', 'customer@example.com' => 'customer'] as $email => $role) {
            $this->assertTrue(User::where('email', $email)->firstOrFail()->hasRole($role));
        }

        Storage::disk('public')->assertExists('products/socket-set-case.jpg');

        $this->get('/')->assertOk()->assertSee('Socket Sets');
        $this->get('/products')->assertOk()->assertSee('Workshop Tool Cart 7 Drawers');
        $this->get('/pages')->assertOk()->assertSee('How to Remove a Broken Glow Plug')->assertDontSee('Plastic Welding in the Car Industry');

        $customer = User::where('email', 'customer@example.com')->first();
        $this->actingAs($customer)->get('/orders')->assertOk()->assertSee(Order::first()->order_number);
    }
}
