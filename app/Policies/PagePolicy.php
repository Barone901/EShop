<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Page;
use App\Models\User;
use App\Policies\Concerns\AuthorizesByPermission;

/**
 * Pristup k admin sekcii: stranky a clanky (maju soft delete, preto aj obnovenie a trvale zmazanie).
 */
class PagePolicy
{
    use AuthorizesByPermission;

    private const PERMISSION = 'manage pages';

    public function restore(User $user, Page $page): bool
    {
        return $this->allowed($user);
    }

    public function restoreAny(User $user): bool
    {
        return $this->allowed($user);
    }

    public function forceDelete(User $user, Page $page): bool
    {
        return $this->allowed($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->allowed($user);
    }
}
