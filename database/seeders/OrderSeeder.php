<?php

// [AI] Vygenerovane s pomocou AI (Claude).

namespace Database\Seeders;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Product;
use App\Models\User;
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
            [OrderStatus::Delivered, PaymentStatus::Paid, 30, ['socket-set-14-46-pcs' => 1, 'combination-pliers-180-mm' => 2], null],
            [OrderStatus::Shipped, PaymentStatus::Pending, 4, ['workshop-tool-cart-7-drawers' => 1], 'Please call before delivery.'],
            [OrderStatus::Pending, PaymentStatus::Pending, 1, ['screwdriver-set-with-stand-18-pcs' => 1, 'vde-screwdriver-set-7-pcs' => 1], null],
            [OrderStatus::Cancelled, PaymentStatus::Pending, 12, ['hub-puller-hydraulic' => 1], null],
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
            $shippingCost = (float) config('shop.shipping_cost');
            $date = now()->subDays($daysAgo);

            $order = Order::create([
                // rovnaky format ako Order::generateOrderNumber(), ale s datumom objednavky
                'order_number' => 'ORD-'.$date->format('Ymd').'-'.Str::upper(Str::random(6)),
                'user_id' => $customer->id,
                'status' => $status,
                'payment_method' => PaymentMethod::CashOnDelivery,
                'payment_status' => $paymentStatus,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $subtotal + $shippingCost,
                'note' => $note,
            ]);

            // created_at nie je fillable, datum objednavky nastavime dodatocne
            $order->forceFill(['created_at' => $date, 'updated_at' => $date])->save();

            $order->items()->createMany($lines->all());

            // Snapshot predvolenych adries zakaznika (typy adries su v oboch tabulkach rovnake)
            foreach (AddressType::cases() as $type) {
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
