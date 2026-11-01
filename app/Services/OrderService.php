<?php

// [AI] Upravene s pomocou AI (Claude): opravy typov a anotacii (upozornenia PhpStorm/PHPStan).

declare(strict_types=1);

namespace App\Services;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Sluzba pre spravu objednavok.
 *
 * [AI] Upravene s pomocou AI (Claude): medzisucet z cien v DB, zamok objednavky pri ruseni,
 *      spolocne rusenie pre zakaznika aj admina, vyhladanie objednavky zakaznika/hosta.
 */
class OrderService
{
    /**
     * Relacie potrebne pre detail objednavky.
     */
    private const DETAIL_RELATIONS = ['items.product', 'shippingAddress', 'billingAddress'];

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
                // 1) Produkty zamkneme jednym dotazom (lockForUpdate), aby sa sklad
                //    neprebil so suzbeznymi objednavkami, a skontrolujeme dostupnost
                $items = collect($cart['items']);

                $products = Product::query()
                    ->with('category')
                    ->whereKey($items->pluck('id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $lines = $items->map(function (array $cartItem) use ($products) {
                    $quantity = (int) $cartItem['quantity'];
                    $product = $products->get($cartItem['id']);

                    if (!$product || !$product->isVisible()) {
                        throw new OrderException("Product \"{$cartItem['name']}\" is no longer available.");
                    }

                    if ($product->stock < $quantity) {
                        throw new OrderException("Not enough stock for \"{$product->name}\". Available: {$product->stock}.");
                    }

                    // Cenu berieme z DB (zamknuty riadok), nie z kosika
                    $unitPrice = (float) $product->price;

                    return [
                        'product' => $product,
                        'price' => $unitPrice,
                        'quantity' => $quantity,
                        'subtotal' => $unitPrice * $quantity,
                    ];
                });

                // 2) Objednavka - medzisucet z tych istych cien ako polozky
                $subtotal = $lines->sum('subtotal');
                $shippingCost = (float) config('shop.shipping_cost');

                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'user_id' => Auth::id(),
                    'guest_token' => $guestToken,
                    'guest_email' => Auth::check() ? null : ($orderData['shipping_email'] ?? null),

                    'status' => OrderStatus::Pending,
                    'payment_method' => PaymentMethod::CashOnDelivery,
                    'payment_status' => PaymentStatus::Pending,

                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'total' => $subtotal + $shippingCost,

                    'note' => $orderData['note'] ?? null,
                ]);

                // 3) Snapshot adries
                foreach (AddressType::cases() as $type) {
                    $order->addresses()->create(OrderAddress::attributesFromCheckout($orderData, $type));
                }

                // 4) Polozky + odpocet skladu
                foreach ($lines as $line) {
                    $order->items()->create([
                        'product_id' => $line['product']->id,
                        'product_name' => $line['product']->name,
                        'price' => $line['price'],
                        'quantity' => $line['quantity'],
                        'subtotal' => $line['subtotal'],
                    ]);

                    $line['product']->decrement('stock', $line['quantity']);
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
     * Ziska vsetky objednavky pouzivatela (s poctom poloziek, bez nacitania poloziek samotnych).
     */
    public function getUserOrders(User $user): Collection
    {
        return $user->orders()
            ->withCount('items')
            ->latest()
            ->get();
    }

    /**
     * Najde objednavku prihlaseneho pouzivatela podla cisla (cudziu objednavku nevrati).
     */
    public function findUserOrder(User $user, string $orderNumber): ?Order
    {
        return $user->orders()
            ->with(self::DETAIL_RELATIONS)
            ->where('order_number', $orderNumber)
            ->first();
    }

    /**
     * Najde objednavku hosta podla cisla, len ak sedi token ulozeny v jeho session.
     */
    public function findGuestOrder(string $orderNumber, mixed $guestToken): ?Order
    {
        if (!is_string($guestToken)) {
            return null;
        }

        $order = Order::with(self::DETAIL_RELATIONS)
            ->where('order_number', $orderNumber)
            ->whereNotNull('guest_token')
            ->first();

        return $order !== null && is_string($order->guest_token) && hash_equals($order->guest_token, $guestToken)
            ? $order
            : null;
    }

    /**
     * Zrusi objednavku a vrati produkty na sklad.
     * Zakaznik moze zrusit objednavku pred spracovanim, admin kym nebola odoslana
     * (pri zruseni adminom sa platba oznaci ako neuspesna).
     *
     * @throws OrderException ak objednavku nie je mozne zrusit
     */
    public function cancelOrder(Order $order, bool $byAdmin = false): void
    {
        try {
            DB::transaction(function () use ($order, $byAdmin) {
                // Stav overime az pod zamkom objednavky - dva subezne requesty (napr. dvojklik)
                // by inak oba presli kontrolou a vratili tovar na sklad dvakrat
                $locked = Order::query()->lockForUpdate()->findOrFail((int) $order->getKey());

                if ($locked->isCancelled()) {
                    throw new OrderException('Order is already cancelled.');
                }

                $cancellable = $byAdmin
                    ? $locked->status->isCancellableByAdmin()
                    : $locked->status->isCancellableByCustomer();

                if (!$cancellable) {
                    throw new OrderException('This order can no longer be cancelled.');
                }

                $items = $locked->items()->get();

                $products = Product::query()
                    ->whereKey($items->pluck('product_id')->filter())
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($items as $item) {
                    $products->get($item->product_id)?->increment('stock', $item->quantity);
                }

                $locked->update([
                    'status' => OrderStatus::Cancelled,
                    ...($byAdmin ? ['payment_status' => PaymentStatus::Failed] : []),
                ]);
            });
        } catch (OrderException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            throw new OrderException('Failed to cancel the order. Please try again.', previous: $e);
        }

        $order->refresh();
    }

    /**
     * Zmaze zrusenu objednavku. Polozky a adresy objednavky zmaze databaza
     * (cudzie kluce order_items a order_addresses maju cascadeOnDelete).
     *
     * @throws OrderException ak objednavka nie je zrusena
     */
    public function deleteOrder(Order $order): void
    {
        if (!$order->isDeletable()) {
            throw new OrderException('Only cancelled orders can be deleted.');
        }

        $order->delete();
    }
}
