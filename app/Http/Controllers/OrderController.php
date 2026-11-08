<?php

// [AI] Upravene s pomocou AI (Claude): route model binding {order}, autorizacia cez OrderPolicy,
// chyby objednavky spracuje OrderException::render() (controller nema try/catch).

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\AddressType;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Controller pre spracovanie objednavok a checkout procesu.
 *
 * Objednavku v URL najde Laravel sam podla cisla (route model binding, Order::getRouteKeyName),
 * pristup kontroluje OrderPolicy - cudzia objednavka vrati 404.
 */
class OrderController extends Controller implements HasMiddleware
{
    /**
     * Kluc session s tokenom objednavky hosta (host vidi potvrdenie len svojej objednavky).
     */
    private const GUEST_TOKEN_SESSION_KEY = 'guest_order_token';

    /**
     * Injektuje sluzby pre objednavky a kosik.
     */
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CartService $cartService
    ) {}

    /**
     * Autorizacia akcii nad objednavkou zakaznika (OrderPolicy).
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:view,order', only: ['show']),
            new Middleware('can:cancel,order', only: ['cancel']),
            new Middleware('can:delete,order', only: ['destroy']),
        ];
    }

    /**
     * Zobrazi checkout stranku s formularom pre dorucenie a fakturacne udaje.
     */
    public function checkout(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if ($cart['count'] === 0) {
            return redirect()->route('cart.index')
                ->with('error', __('Your cart is empty.'));
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
            'defaultCountry' => config('shop.default_country'),
            // Vzory pre kontrolu poli v prehliadaci (rovnake ako validacia na serveri)
            'patterns' => CheckoutRequest::PATTERNS,
        ]);
    }

    /**
     * Spracuje a ulozi novu objednavku.
     */
    public function store(CheckoutRequest $request): RedirectResponse
    {
        $order = $this->orderService->createOrder($request->orderData(), $request->user());

        // Session je HTTP vrstva - token hosta uklada controller, nie sluzba
        if ($order->guest_token) {
            $request->session()->put(self::GUEST_TOKEN_SESSION_KEY, $order->guest_token);
        }

        return redirect()->route('orders.confirmation', $order)
            ->with('success', __('Order has been successfully created.'));
    }

    /**
     * Zobrazi potvrdenie objednavky po uspesnom dokonceni.
     * Prihlaseny pouzivatel vidi len svoje objednavky, host len tu, ktorej token ma v session.
     */
    public function confirmation(Request $request, Order $order): View
    {
        Gate::authorize('viewConfirmation', [$order, $request->session()->get(self::GUEST_TOKEN_SESSION_KEY)]);

        return view('pages.checkout.confirmation', [
            'order' => $order->loadDetails(),
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
    public function show(Order $order): View
    {
        return view('pages.orders.show', [
            'order' => $order->loadDetails(),
        ]);
    }

    /**
     * Zrusi objednavku a vrati produkty na sklad.
     */
    public function cancel(Order $order): RedirectResponse
    {
        $this->orderService->cancelOrder($order);

        return redirect()->route('orders.show', $order)
            ->with('success', __('Order has been cancelled.'));
    }

    /**
     * Zmaze zrusenu objednavku z uctu pouzivatela.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $this->orderService->deleteOrder($order);

        return redirect()->route('orders.index')
            ->with('success', __('Order :number has been deleted.', ['number' => $order->order_number]));
    }
}
