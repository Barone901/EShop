<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BannerPolicy
{
    use HandlesAuthorization;

    /**
     * Zobraziť zoznam bannerov (sekcia media).
     * Tu používaš permission: manage media
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage media');
    }

    /**
     * Zobraziť detail bannera.
     */
    public function view(User $user, Banner $banner): bool
    {
        return $user->can('manage media');
    }

    /**
     * Vytvoriť nový banner.
     */
    public function create(User $user): bool
    {
        return $user->can('manage media');
    }

    /**
     * Upraviť banner.
     */
    public function update(User $user, Banner $banner): bool
    {
        return $user->can('manage media');
    }

    /**
     * Vymazať banner.
     */
    public function delete(User $user, Banner $banner): bool
    {
        return $user->can('manage media');
    }

    /**
     * Hromadné mazanie bannerov (bulk delete).
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('manage media');
    }

    /**
     * Obnoviť banner (ak používaš SoftDeletes).
     */
    public function restore(User $user, Banner $banner): bool
    {
        return $user->can('manage media');
    }

    /**
     * Trvalo vymazať banner z databázy.
     */
    public function forceDelete(User $user, Banner $banner): bool
    {
        return $user->can('manage media');
    }
}
