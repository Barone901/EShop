<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\AddressType;
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
 *
 * [AI] Upravene s pomocou AI (Claude): spolocne vyhladanie objednavky (cudzia/neexistujuca = 404).
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

        // Ulozene adresy (len pre prihlaseneho), predvolena je v kazdom zozname prva
        $shippingAddresses = $user?->addressesOfType(AddressType::Shipping) ?? collect();
        $billingAddresses = $user?->addressesOfType(AddressType::Billing) ?? collect();

        return view('pages.checkout.index', [
            'cart' => $cart,
            'user' => $user,
            'isGuest' => !$user,
            'defaultShippingAddress' => $shippingAddresses->first(),
            'defaultBillingAddress' => $billingAddresses->first(),
            'shippingAddresses' => $shippingAddresses,
            'billingAddresses' => $billingAddresses,
            // Vzory pre kontrolu poli v prehliadaci (rovnake ako validacia na serveri)
            'patterns' => CheckoutRequest::PATTERNS,
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
    public function confirmation(Request $request, string $orderNumber): View
    {
        $order = $request->user()
            ? $this->orderService->findUserOrder($request->user(), $orderNumber)
            : $this->orderService->findGuestOrder($orderNumber, $request->session()->get('guest_order_token'));

        abort_if($order === null, 404);

        return view('pages.checkout.confirmation', [
            'order' => $order,
        ]);
    }

    /**
     * Zobrazi zoznam objednavok prihlaseneho pouzivatela.
     */
    public function index(Request $request): View
    {
        return view('pages.orders.index', [
            'orders' => $this->orderService->getUserOrders($request->user()),
        ]);
    }

    /**
     * Zobrazi detail konkretnej objednavky.
     */
    public function show(Request $request, string $orderNumber): View
    {
        return view('pages.orders.show', [
            'order' => $this->userOrder($request, $orderNumber),
        ]);
    }

    /**
     * Zrusi objednavku a vrati produkty na sklad.
     */
    public function cancel(Request $request, string $orderNumber): RedirectResponse
    {
        $order = $this->userOrder($request, $orderNumber);

        try {
            $this->orderService->cancelOrder($order);
        } catch (OrderException $e) {
            return redirect()->route('orders.show', $orderNumber)->with('error', $e->getMessage());
        }

        return redirect()->route('orders.show', $orderNumber)
            ->with('success', 'Order has been cancelled.');
    }

    /**
     * Zmaze zrusenu objednavku z uctu pouzivatela.
     */
    public function destroy(Request $request, string $orderNumber): RedirectResponse
    {
        $order = $this->userOrder($request, $orderNumber);

        try {
            $this->orderService->deleteOrder($order);
        } catch (OrderException $e) {
            return redirect()->route('orders.show', $orderNumber)->with('error', $e->getMessage());
        }

        return redirect()->route('orders.index')
            ->with('success', "Order {$orderNumber} has been deleted.");
    }

    /**
     * Objednavka prihlaseneho pouzivatela, inak 404 (cudziu objednavku neprezradime).
     */
    private function userOrder(Request $request, string $orderNumber): Order
    {
        return $this->orderService->findUserOrder($request->user(), $orderNumber) ?? abort(404);
    }
}
