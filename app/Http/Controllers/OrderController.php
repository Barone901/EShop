<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\OrderException;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller pre spracovanie objednavok a checkout procesu.
 */
class OrderController extends Controller
{
    /**
     * Injektuje sluzby pre objednavky a kosik.
     */
    public function __construct(
        private OrderService $orderService,
        private CartService $cartService
    ) {}

    /**
     * Zobrazi checkout stranku s formularom pre dorucenie a fakturacne udaje.
     */
    public function checkout(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if ($cart['count'] === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $user = $request->user();

        return view('pages.checkout.index', [
            'cart' => $cart,
            'user' => $user,
            'isGuest' => !$user,
        ]);
    }

    /**
     * Spracuje a ulozi novu objednavku.
     */
    public function store(CheckoutRequest $request): RedirectResponse
    {
        try {
            $order = $this->orderService->createOrder($request->validated());
        } catch (OrderException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('orders.confirmation', $order->order_number)
            ->with('success', 'Order has been successfully created.');
    }

    /**
     * Zobrazi potvrdenie objednavky po uspesnom dokonceni.
     * Prihlaseny pouzivatel vidi len svoje objednavky, host len tu, ktorej token ma v session.
     */
    public function confirmation(Request $request, string $orderNumber): View|RedirectResponse
    {
        $order = $this->orderService->getOrderByNumber($orderNumber, $request->user()?->id);

        if (!$order || (!$request->user() && !$this->isGuestOwner($request, $order))) {
            return redirect()->route('home')
                ->with('error', 'Order not found.');
        }

        return view('pages.checkout.confirmation', [
            'order' => $order,
        ]);
    }

    /**
     * Overi, ze host ma v session token prave tejto objednavky.
     */
    private function isGuestOwner(Request $request, Order $order): bool
    {
        $token = $request->session()->get('guest_order_token');

        return is_string($token)
            && $order->guest_token !== null
            && hash_equals($order->guest_token, $token);
    }
}
