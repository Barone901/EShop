<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Database\Seeders;

use App\Enums\AddressType;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Ukazkovi pouzivatelia pre kazdu rolu (okrem admina, ktoreho vytvara AdminUserSeeder).
 * Vsetci maju heslo "password".
 */
class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->createUser('employee@example.com', 'Eva', 'Zamestnana', UserRole::Employee->value);
        $this->createUser('designer@example.com', 'Dano', 'Dizajner', UserRole::Designer->value);

        $customer = $this->createUser('customer@example.com', 'Jana', 'Novakova', UserRole::Customer->value);

        $customer->addresses()->createMany([
            [
                'type' => AddressType::Shipping,
                'is_default' => true,
                'first_name' => 'Jana',
                'last_name' => 'Novakova',
                'email' => 'customer@example.com',
                'phone' => '+421 905 123 456',
                'street' => 'Hlavna 12',
                'city' => 'Zilina',
                'postal_code' => '010 01',
                'country' => 'Slovakia',
            ],
            [
                'type' => AddressType::Shipping,
                'is_default' => false,
                'first_name' => 'Jana',
                'last_name' => 'Novakova',
                'email' => 'customer@example.com',
                'phone' => '+421 905 123 456',
                'street' => 'Kollarova 88',
                'city' => 'Martin',
                'postal_code' => '036 01',
                'country' => 'Slovakia',
            ],
            [
                'type' => AddressType::Billing,
                'is_default' => true,
                'first_name' => 'Jana',
                'last_name' => 'Novakova',
                'email' => 'customer@example.com',
                'phone' => '+421 905 123 456',
                'street' => 'Hlavna 12',
                'city' => 'Zilina',
                'postal_code' => '010 01',
                'country' => 'Slovakia',
                'company_name' => 'Autoservis Novak s.r.o.',
                'ico' => '12345678',
                'dic' => '2023456789',
            ],
        ]);
    }

    private function createUser(string $email, string $firstName, string $lastName, string $role): User
    {
        $user = User::create([
            'name' => "{$firstName} {$lastName}",
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => Hash::make('password'),
        ]);

        // email_verified_at nie je fillable, ukazkove ucty su overene
        $user->forceFill(['email_verified_at' => now()])->save();
        $user->assignRole($role);

        return $user;
    }
}
