<?php

// Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene.

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        // Registracia priraduje rolu "customer" (v produkcii ju vytvara RolesAndPermissionsSeeder)
        Role::create(['name' => 'customer', 'guard_name' => 'web']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test.user@gmail.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertTrue(User::firstWhere('email', 'test.user@gmail.com')->hasRole('customer'));
    }
}
