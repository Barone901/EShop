<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

/**
 * Sluzba pre pracu s kategoriami produktov.
 */
class CategoryService
{
    /**
     * Ziska vsetky hlavne kategorie s detskymi kategoriami.
     */
    public function getParentCategories(): Collection
    {
        return Category::parents()
            ->active()
            ->ordered()
            ->with(['children' => fn ($q) => $q->active()])
            ->get();
    }
}
