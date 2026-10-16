<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * VIEW ANY (Filament: zobraziť zoznam kategórií)
     * - rozhoduje, či user vôbec uvidí sekciu kategórií v admin paneli.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage categories');
    }

    /**
     * VIEW (Filament: zobraziť detail konkrétnej kategórie)
     * - napr. keď otvoríš konkrétnu kategóriu na detail.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->can('manage categories');
    }

    /**
     * CREATE (Filament: vytvoriť kategóriu)
     * - kto môže pridávať nové kategórie.
     */
    public function create(User $user): bool
    {
        return $user->can('manage categories');
    }

    /**
     * UPDATE (Filament: upraviť kategóriu)
     * - kto môže meniť existujúce kategórie.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->can('manage categories');
    }

    /**
     * DELETE (Filament: vymazať kategóriu)
     * - klasické mazanie (ak je to soft delete alebo hard delete závisí od modelu).
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->can('manage categories');
    }

    /**
     * DELETE ANY (Filament: bulk delete – hromadné mazanie)
     * - keď v zozname označíš viac položiek a dáš "Delete selected".
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('manage categories');
    }

    /**
     * RESTORE (Filament: obnoviť z koša)
     * - funguje len ak používaš SoftDeletes.
     */
    public function restore(User $user, Category $category): bool
    {
        return $user->can('manage categories');
    }

    /**
     * FORCE DELETE (Filament: trvalo vymazať)
     * - natrvalo odstráni z DB (ak Filament/SoftDeletes umožní).
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return $user->can('manage categories');
    }
}
