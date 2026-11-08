<?php

// [AI] Upravene s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Services;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Sluzba pre spravu objednavok.
 *
 * Nepracuje s HTTP vrstvou (Auth, session) - pouzivatela dostane ako parameter od controllera
 * a token hosta ulozi do session az controller. Da sa tak volat z webu, adminu aj testu.
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
     * Pre hosta ($user = null) vygeneruje guest_token - controller ho ulozi do session,
     * aby host videl potvrdenie len svojej objednavky.
     *
     * @param array{email: string, note: ?string, addresses: array<string, array<string, mixed>>} $orderData
     *                                                                                                       data pripravene CheckoutRequest::orderData() (adresy podla AddressType)
     *
     * @throws OrderException ak objednavku nie je mozne vytvorit (sprava je pre zakaznika)
     */
    public function createOrder(array $orderData, ?User $user): Order
    {
        $cart = $this->cartService->getCart();

        if ($cart['count'] === 0) {
            throw new OrderException(__('Your cart is empty.'));
        }

        $guestToken = $user ? null : Str::random(64);

        try {
            $order = DB::transaction(function () use ($orderData, $cart, $guestToken, $user) {
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
                        throw new OrderException(__('Product ":name" is no longer available.', ['name' => $cartItem['name']]));
                    }

                    if ($product->stock < $quantity) {
                        throw new OrderException(__('Not enough stock for ":name". Available: :stock.', ['name' => $product->name, 'stock' => $product->stock]));
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
                    'user_id' => $user?->id,
                    'guest_token' => $guestToken,
                    'guest_email' => $user ? null : $orderData['email'],

                    'status' => OrderStatus::Pending,
                    'payment_method' => PaymentMethod::CashOnDelivery,
                    'payment_status' => PaymentStatus::Pending,

                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'total' => $subtotal + $shippingCost,

                    'note' => $orderData['note'] ?? null,
                ]);

                // 3) Snapshot adries (dorucovacia + fakturacna)
                foreach (AddressType::cases() as $type) {
                    $order->addresses()->create([
                        'type' => $type,
                        ...$orderData['addresses'][$type->value],
                    ]);
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

            throw new OrderException(__('An error occurred while creating the order. Please try again.'), previous: $e);
        }

        // Az po uspesnom commite vyprazdnime kosik
        $this->cartService->clear();

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
     * Ziska poslednych N objednavok pouzivatela (prehlad uctu).
     */
    public function getRecentUserOrders(User $user, int $limit = 5): Collection
    {
        return $user->orders()
            ->latest()
            ->take($limit)
            ->get();
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
                    throw new OrderException(__('Order is already cancelled.'));
                }

                $cancellable = $byAdmin
                    ? $locked->status->isCancellableByAdmin()
                    : $locked->status->isCancellableByCustomer();

                if (!$cancellable) {
                    throw new OrderException(__('This order can no longer be cancelled.'));
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

            throw new OrderException(__('Failed to cancel the order. Please try again.'), previous: $e);
        }

        $order->refresh();
    }

    /**
     * Posunie objednavku na dalsi stav spracovania (admin). Povolene prechody urcuje OrderStatus::nextStatuses().
     * Pri doruceni dobierky sa platba oznaci ako zaplatena (platba prebehla pri prevzati).
     *
     * @throws OrderException ak prechod nie je povoleny
     */
    public function advanceStatus(Order $order, OrderStatus $status): void
    {
        DB::transaction(function () use ($order, $status) {
            // Stav overime pod zamkom - dvaja admini naraz nesmu preskocit krok
            $locked = Order::query()->lockForUpdate()->findOrFail((int) $order->getKey());

            if (!$locked->status->canAdvanceTo($status)) {
                throw new OrderException(__('Order cannot be changed from :from to :to.', [
                    'from' => $locked->status->getLabel(),
                    'to' => $status->getLabel(),
                ]));
            }

            $paidOnDelivery = $status === OrderStatus::Delivered
                && $locked->payment_method === PaymentMethod::CashOnDelivery;

            $locked->update([
                'status' => $status,
                ...($paidOnDelivery ? ['payment_status' => PaymentStatus::Paid] : []),
            ]);
        });

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
            throw new OrderException(__('Only cancelled orders can be deleted.'));
        }

        $order->delete();
    }
}
