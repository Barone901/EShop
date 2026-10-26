<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Ukazkove objednavky zakaznika v roznych stavoch.
 */
class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'customer@example.com')->firstOrFail();
        $products = Product::orderBy('id')->get()->keyBy('slug');

        $orders = [
            // [stav, stav platby, pred kolkymi dnami, [slug produktu => mnozstvo], poznamka]
            [Order::STATUS_DELIVERED, Order::PAYMENT_PAID, 30, ['socket-set-14-46-pcs' => 1, 'combination-pliers-180-mm' => 2], null],
            [Order::STATUS_SHIPPED, Order::PAYMENT_PENDING, 4, ['workshop-tool-cart-7-drawers' => 1], 'Please call before delivery.'],
            [Order::STATUS_PENDING, Order::PAYMENT_PENDING, 1, ['screwdriver-set-with-stand-18-pcs' => 1, 'vde-screwdriver-set-7-pcs' => 1], null],
            [Order::STATUS_CANCELLED, Order::PAYMENT_PENDING, 12, ['hub-puller-hydraulic' => 1], null],
        ];

        foreach ($orders as [$status, $paymentStatus, $daysAgo, $items, $note]) {
            $lines = collect($items)->map(function (int $quantity, string $slug) use ($products) {
                $product = $products->get($slug);

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
            });

            $subtotal = $lines->sum('subtotal');
            $date = now()->subDays($daysAgo);

            $order = Order::create([
                // rovnaky format ako Order::generateOrderNumber(), ale s datumom objednavky
                'order_number' => 'ORD-'.$date->format('Ymd').'-'.strtoupper(Str::random(4)),
                'user_id' => $customer->id,
                'status' => $status,
                'payment_method' => Order::PAYMENT_COD,
                'payment_status' => $paymentStatus,
                'subtotal' => $subtotal,
                'shipping_cost' => Order::SHIPPING_COST,
                'total' => $subtotal + Order::SHIPPING_COST,
                'note' => $note,
            ]);

            // created_at nie je fillable, datum objednavky nastavime dodatocne
            $order->forceFill(['created_at' => $date, 'updated_at' => $date])->save();

            $order->items()->createMany($lines->all());

            // Snapshot predvolenych adries zakaznika (typy adries su v oboch tabulkach rovnake)
            foreach (array_keys(UserAddress::TYPES) as $type) {
                $address = $customer->addresses()
                    ->where('type', $type)
                    ->where('is_default', true)
                    ->firstOrFail();

                $order->addresses()->create([
                    'type' => $type,
                    ...Arr::except($address->only((new OrderAddress)->getFillable()), ['order_id', 'type']),
                ]);
            }
        }
    }
}
