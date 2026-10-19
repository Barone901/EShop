<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Sluzba pre spravu objednavok.
 */
class OrderService
{
    /**
     * Injektuje sluzbu pre kosik.
     */
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Vytvori novu objednavku v transakcii s kontrolou skladu.
     *
     * @throws OrderException ak objednavku nie je mozne vytvorit (sprava je pre zakaznika)
     */
    public function createOrder(array $orderData): Order
    {
        $cart = $this->cartService->getCart();

        if ($cart['count'] === 0) {
            throw new OrderException('Your cart is empty.');
        }

        $guestToken = Auth::check() ? null : Str::random(64);

        try {
            $order = DB::transaction(function () use ($orderData, $cart, $guestToken) {
                $shippingCost = Order::SHIPPING_COST;
                $subtotal = (float) $cart['total'];
                $total = $subtotal + $shippingCost;

                // 1) Objednavka
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'user_id' => Auth::id(),
                    'guest_token' => $guestToken,
                    'guest_email' => Auth::check() ? null : ($orderData['shipping_email'] ?? null),

                    'status' => Order::STATUS_PENDING,
                    'payment_method' => Order::PAYMENT_COD,
                    'payment_status' => Order::PAYMENT_PENDING,

                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'total' => $total,

                    'note' => $orderData['note'] ?? null,
                ]);

                // 2) Snapshot adries
                $order->addresses()->create(
                    OrderAddress::attributesFromCheckout($orderData, OrderAddress::TYPE_SHIPPING)
                );

                $order->addresses()->create(
                    OrderAddress::attributesFromCheckout($orderData, OrderAddress::TYPE_BILLING)
                );

                // 3) Polozky + odpocet skladu. Produkty zamkneme jednym dotazom (lockForUpdate),
                //    aby sa sklad neprebil so suzbeznymi objednavkami.
                $items = collect($cart['items']);

                $products = Product::query()
                    ->whereKey($items->pluck('id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($items as $cartItem) {
                    $qty = (int) $cartItem['quantity'];
                    $product = $products->get($cartItem['id']);

                    if (!$product) {
                        throw new OrderException("Product \"{$cartItem['name']}\" is no longer available.");
                    }

                    if ($product->stock < $qty) {
                        throw new OrderException("Not enough stock for \"{$product->name}\". Available: {$product->stock}.");
                    }

                    // Cenu berieme z DB, nie z kosika (session)
                    $unitPrice = (float) $product->price;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $unitPrice,
                        'quantity' => $qty,
                        'subtotal' => $unitPrice * $qty,
                    ]);

                    $product->decrement('stock', $qty);
                }

                return $order;
            });
        } catch (OrderException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // Interne detaily (napr. SQL chyba) zalogujeme, zakaznikovi ich neukazujeme
            report($e);

            throw new OrderException('An error occurred while creating the order. Please try again.', previous: $e);
        }

        // Az po uspesnom commite: vyprazdnit kosik a ulozit token hosta
        $this->cartService->clear();

        if ($guestToken) {
            session(['guest_order_token' => $guestToken]);
        }

        return $order;
    }

    /**
     * Ziska objednavku podla cisla objednavky.
     */
    public function getOrderByNumber(string $orderNumber, ?int $userId = null): ?Order
    {
        $query = Order::with(['items.product', 'shippingAddress', 'billingAddress'])
            ->where('order_number', $orderNumber);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->first();
    }
}
