<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Zobraziť zoznam objednávok v administrácii.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage orders');
    }

    /**
     * Zobraziť detail konkrétnej objednávky.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->can('manage orders');
    }

    /**
     * Vytvoriť objednávku cez Filament admin.
     *
     * Tu to máš nastavené na FALSE, lebo:
     * - objednávky vytvára zákazník cez e-shop (front-end),
     * - admin ich iba spracováva (mení status, poznámku, dopravu…).
     *
     * Ak by si niekedy chcel umožniť manuálne vytváranie objednávok v admin,
     * stačí vrátiť: return $user->can('manage orders');
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Upraviť objednávku (typicky zmena statusu, platby, poznámky…).
     */
    public function update(User $user, Order $order): bool
    {
        return $user->can('manage orders');
    }

    /**
     * Vymazať objednávku.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->can('manage orders');
    }

    /**
     * Hromadné mazanie objednávok (bulk delete).
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('manage orders');
    }

    /**
     * Obnoviť objednávku (ak používaš SoftDeletes).
     */
    public function restore(User $user, Order $order): bool
    {
        return $user->can('manage orders');
    }

    /**
     * Trvalo vymazať objednávku z DB.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return $user->can('manage orders');
    }
}
