<?php

// [AI] Upravene s pomocou AI (Claude): pravidla pre zakaznika (view, cancel, delete, viewConfirmation).

declare(strict_types=1);

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Policies\Concerns\AuthorizesByPermission;
use Illuminate\Auth\Access\Response;

/**
 * Objednavky: admin sekcia (opravnenie "manage orders") aj ucet zakaznika.
 * Objednavky vytvara len zakaznik cez eshop, v admine sa iba spracuvaju (stav, platba).
 *
 * Cudziu objednavku zakaznik nedostane ani ako 403 - odpoved je 404 (denyAsNotFound),
 * aby sa nedalo zistit, ze objednavka s danym cislom existuje.
 */
class OrderPolicy
{
    use AuthorizesByPermission;

    private const PERMISSION = 'manage orders';

    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Detail objednavky: admin s opravnenim alebo vlastnik.
     */
    public function view(User $user, Order $order): Response
    {
        return $this->allowed($user) || $this->owns($user, $order)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Zrusenie v ucte zakaznika: len vlastnik (ci sa este da zrusit, rozhodne OrderService).
     */
    public function cancel(User $user, Order $order): Response
    {
        return $this->owns($user, $order) ? Response::allow() : Response::denyAsNotFound();
    }

    /**
     * Zmazanie: admin s opravnenim alebo vlastnik (vlastnik len zrusenu - kontroluje OrderService).
     */
    public function delete(User $user, Order $order): Response
    {
        return $this->view($user, $order);
    }

    /**
     * Potvrdenie po objednani: prihlaseny vlastnik, alebo host s tokenom objednavky zo svojej session.
     */
    public function viewConfirmation(?User $user, Order $order, mixed $guestToken = null): Response
    {
        if ($user) {
            return $this->owns($user, $order) ? Response::allow() : Response::denyAsNotFound();
        }

        $tokenMatches = is_string($guestToken)
            && is_string($order->guest_token)
            && hash_equals($order->guest_token, $guestToken);

        return $tokenMatches ? Response::allow() : Response::denyAsNotFound();
    }

    private function owns(User $user, Order $order): bool
    {
        return $order->user_id !== null && (int) $order->user_id === (int) $user->id;
    }
}
