<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Zobraziť zoznam produktov v administrácii.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage products');
    }

    /**
     * Zobraziť detail konkrétneho produktu.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->can('manage products');
    }

    /**
     * Vytvoriť nový produkt.
     */
    public function create(User $user): bool
    {
        return $user->can('manage products');
    }

    /**
     * Upraviť existujúci produkt.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->can('manage products');
    }

    /**
     * Vymazať produkt.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->can('manage products');
    }

    /**
     * Hromadné mazanie produktov (bulk delete).
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('manage products');
    }

    /**
     * Obnoviť produkt (ak používaš SoftDeletes).
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->can('manage products');
    }

    /**
     * Trvalo vymazať produkt z databázy.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->can('manage products');
    }
}
