<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Support\InputPattern;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kontrola platnych hodnot vstupov (nielen typu) na serveri
 * a rovnake vzory vo formularoch pre kontrolu v prehliadaci.
 */
class InputValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_patterns_accept_valid_and_reject_invalid_values(): void
    {
        $cases = [
            [InputPattern::PERSON_NAME, ["Ján Nováková", "O'Neil", 'Anna-Mária'], ['John 2', '<script>', ' ']],
            [InputPattern::PHONE, ['+421 905 123 456', '0905123456', '041/123-4567'], ['abc', '12', '+421 905 123 456 789 000']],
            [InputPattern::POSTAL_CODE, ['010 01', '03601', 'SW1A 1AA'], ['0', '010_01', '12345678901']],
            [InputPattern::CITY, ['Zilina', 'Bratislava 4', 'Banská Bystrica'], ['4 Bratislava', 'Zilina!']],
            [InputPattern::COMPANY_ID, ['12345678', 'SK2023456789'], ['SK 2023', '123-456']],
        ];

        foreach ($cases as [$pattern, $valid, $invalid]) {
            $regex = '/^(?:' . $pattern . ')$/u';

            foreach ($valid as $value) {
                $this->assertMatchesRegularExpression($regex, $value);
            }

            foreach ($invalid as $value) {
                $this->assertDoesNotMatchRegularExpression($regex, $value);
            }
        }
    }

    public function test_checkout_rejects_invalid_phone_and_postal_code(): void
    {
        $product = Product::factory()->create(['price' => 10, 'stock' => 5]);
        $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 1]);

        $this->get('/checkout')
            ->assertOk()
            ->assertSee('pattern="' . e(InputPattern::PHONE) . '"', false)
            ->assertSee('data-pattern-message="' . e(InputPattern::message(InputPattern::PHONE)) . '"', false)
            ->assertSee('data-validate', false);

        $this->post('/orders', [
            'shipping_name' => 'John Doe',
            'shipping_email' => 'john@gmail.com',
            'shipping_phone' => 'call me',
            'shipping_address' => 'Main Street 1',
            'shipping_city' => 'Martin',
            'shipping_postal_code' => '###',
            'billing_name' => 'John Doe',
            'billing_email' => 'john@gmail.com',
            'billing_address' => 'Main Street 1',
            'billing_city' => 'Martin',
            'billing_postal_code' => '03601',
        ])->assertSessionHasErrors([
            'shipping_phone' => InputPattern::message(InputPattern::PHONE),
            'shipping_postal_code' => InputPattern::message(InputPattern::POSTAL_CODE),
        ]);
    }

    public function test_address_form_rejects_invalid_values(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/addresses', [
                'type' => 'shipping',
                'first_name' => 'John1',
                'last_name' => 'Doe',
                'street' => 'Main Street 1',
                'city' => 'Martin',
                'postal_code' => '03601',
                'ico' => '12 34',
            ])
            ->assertSessionHasErrors(['first_name', 'ico'])
            ->assertSessionDoesntHaveErrors(['last_name', 'city', 'postal_code']);
    }
}
