<?php

// [AI] Vygenerovane s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserAddress;

/**
 * Pouzivatel moze pracovat len so svojimi adresami.
 */
class UserAddressPolicy
{
    public function update(User $user, UserAddress $address): bool
    {
        return (int) $address->user_id === (int) $user->id;
    }

    public function delete(User $user, UserAddress $address): bool
    {
        return $this->update($user, $address);
    }
}
