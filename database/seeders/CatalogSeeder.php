<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Database\Seeders;

use App\Models\Category;
use Database\Seeders\Concerns\CopiesDemoImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Ukazkove kategorie a produkty (naradie pre autoservisy).
 */
class CatalogSeeder extends Seeder
{
    use CopiesDemoImages;

    public function run(): void
    {
        $catalog = [
            [
                'name' => 'Socket Sets',
                'description' => 'Socket wrench sets in sturdy cases for workshops and mobile service.',
                'image' => 'socket-case.png',
                'products' => [
                    ['Socket Set 1/4" 46 pcs', 'Compact set of sockets, bits and a fine-tooth ratchet in a blue case.', 89.90, 14, 'socket-set-case.jpg'],
                    ['Socket Set 1/2" 24 pcs', 'Heavy-duty 12-point sockets 10-32 mm with a reversible ratchet.', 129.00, 6, 'socket-case.png'],
                    ['Torx Bit Socket Set 3/8"', 'Torx bit sockets T20-T60 made of S2 steel.', 39.50, 2, null],
                ],
            ],
            [
                'name' => 'Hand Tools',
                'description' => 'Pliers, wrenches, hammers and complete tool kits.',
                'image' => 'tool-kit-roll.jpg',
                'products' => [
                    ['Tool Kit in Roll Bag 22 pcs', 'Basic kit with hammer, saw, pliers, screwdrivers and tape measure.', 74.00, 9, 'tool-kit-roll.jpg'],
                    ['Combination Pliers 180 mm', 'Insulated combination pliers up to 1000 V.', 18.90, 25, null],
                    ['Adjustable Wrench 250 mm', 'Chrome-vanadium adjustable wrench with a scale.', 22.40, 0, null],
                ],
            ],
            [
                'name' => 'Screwdrivers',
                'description' => 'Screwdrivers and screwdriver sets for every type of screw.',
                'image' => 'screwdriver-stand.png',
                'products' => [
                    ['Screwdriver Set with Stand 18 pcs', 'Slotted, Phillips and Torx screwdrivers on a wall stand.', 59.90, 11, 'screwdriver-stand.png'],
                    ['VDE Screwdriver Set 7 pcs', 'Insulated screwdrivers for electrical work up to 1000 V.', 34.90, 4, null],
                ],
            ],
            [
                'name' => 'Workshop Equipment',
                'description' => 'Tool carts, cabinets and custom foam inserts.',
                'image' => 'workshop-cart.png',
                'products' => [
                    ['Workshop Tool Cart 7 Drawers', 'Lockable cart with 7 drawers and ball-bearing runners.', 649.00, 3, 'workshop-cart.png'],
                    ['Foam Insert for Tool Drawer', 'Custom foam insert made to fit your tools.', 45.00, 30, null],
                ],
            ],
            [
                'name' => 'Special Tools',
                'description' => 'Pullers, glow plug extractors and tools for chassis repair.',
                'image' => null,
                'products' => [
                    ['Glow Plug Extractor Kit', 'Removes broken glow plugs without dismounting the cylinder head.', 389.00, 2, null],
                    ['Hub Puller Hydraulic', 'Hydraulic puller for hubs and bearings of passenger cars and vans.', 279.00, 5, null],
                ],
            ],
        ];

        foreach ($catalog as $sortOrder => $data) {
            $category = Category::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'image_url' => $data['image'] ? $this->demoImage($data['image'], 'categories') : null,
                'is_active' => true,
                'sort_order' => $sortOrder,
            ]);

            foreach ($data['products'] as [$name, $description, $price, $stock, $image]) {
                $category->products()->create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'image_url' => $image ? $this->demoImage($image, 'products') : null,
                ]);
            }
        }

        // Neaktivna kategoria - na frontende sa nezobrazuje (ukazka filtra is_active)
        Category::create([
            'name' => 'Clearance',
            'slug' => 'clearance',
            'description' => 'Hidden category, not shown on the website.',
            'is_active' => false,
            'sort_order' => 99,
        ]);
    }
}
