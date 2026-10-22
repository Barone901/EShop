<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy
{
    use HandlesAuthorization;

    /**
     * ZOBRAZIŤ ZOZNAM (Filament: "List Pages")
     * - kontroluje, či user môže vidieť zoznam stránok v administrácii
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage pages');
    }

    /**
     * ZOBRAZIŤ DETAIL (Filament: "View Page")
     * - kontroluje, či user môže otvoriť detail konkrétnej stránky
     */
    public function view(User $user, Page $page): bool
    {
        return $user->can('manage pages');
    }

    /**
     * VYTVORIŤ (Filament: "Create Page")
     * - kontroluje, či user môže vytvárať nové stránky
     */
    public function create(User $user): bool
    {
        return $user->can('manage pages');
    }

    /**
     * UPRAVIŤ (Filament: "Edit Page")
     * - kontroluje, či user môže upravovať existujúce stránky
     */
    public function update(User $user, Page $page): bool
    {
        return $user->can('manage pages');
    }

    /**
     * VYMAZAŤ (Filament: "Delete Page")
     * - soft delete (ak používaš SoftDeletes)
     */
    public function delete(User $user, Page $page): bool
    {
        return $user->can('manage pages');
    }

    /**
     * HROMADNÉ MAZANIE (Filament: bulk delete)
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('manage pages');
    }

    /**
     * HROMADNÉ OBNOVENIE (Filament: bulk restore)
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('manage pages');
    }

    /**
     * OBNOVIŤ (Filament: "Restore Page")
     * - obnoví soft-deleted záznam
     */
    public function restore(User $user, Page $page): bool
    {
        return $user->can('manage pages');
    }

    /**
     * TRVALO VYMAZAŤ (Filament: "Force Delete Page")
     * - natrvalo odstráni z DB
     */
    public function forceDelete(User $user, Page $page): bool
    {
        return $user->can('manage pages');
    }
}
