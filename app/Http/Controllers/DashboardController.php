<?php

// [AI] Vygenerovane s pomocou AI (Claude) - dotazy presunute zo sablony profile/dashboard.
// [AI] Upravene s pomocou AI (Claude): posledne objednavky z OrderService (dotaz nie je v controlleri).

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller pre prehlad uctu zakaznika (posledne objednavky, adresy, nastavenia profilu).
 */
class DashboardController extends Controller
{
    private const RECENT_ORDERS_LIMIT = 5;

    /**
     * Injektuje sluzbu pre objednavky.
     */
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('profile.dashboard', [
            'user' => $user,
            'recentOrders' => $this->orderService->getRecentUserOrders($user, self::RECENT_ORDERS_LIMIT),
            'shippingAddress' => $user->default_shipping_address,
            'billingAddress' => $user->default_billing_address,
            // Vzory pre kontrolu formulara profilu v prehliadaci (rovnake ako na serveri)
            'profilePatterns' => ProfileUpdateRequest::PATTERNS,
        ]);
    }
}
