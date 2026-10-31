<?php

// [AI] Vygenerovane s pomocou AI (Claude) - spolocna logika policies pre admin panel.

declare(strict_types=1);

namespace App\Policies\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Policy pre sekciu admin panelu: vsetky akcie povoli jedno opravnenie (napr. "manage products").
 * Trieda, ktora trait pouziva, definuje konstantu PERMISSION.
 */
trait AuthorizesByPermission
{
    public function viewAny(User $user): bool
    {
        return $this->allowed($user);
    }

    public function view(User $user, Model $model): bool
    {
        return $this->allowed($user);
    }

    public function create(User $user): bool
    {
        return $this->allowed($user);
    }

    public function update(User $user, Model $model): bool
    {
        return $this->allowed($user);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->allowed($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allowed($user);
    }

    protected function allowed(User $user): bool
    {
        return $user->can(self::PERMISSION);
    }
}
