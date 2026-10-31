<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\AuthorizesByPermission;

/**
 * Pristup k admin sekcii: objednavky.
 * Objednavky vytvara len zakaznik cez eshop, v admine sa iba spracuvaju (stav, platba).
 */
class OrderPolicy
{
    use AuthorizesByPermission;

    private const PERMISSION = 'manage orders';

    public function create(User $user): bool
    {
        return false;
    }
}
