<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

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

    /**
     * Ziska aktivne kategorie zoradene podla nazvu (filter vo vypise produktov).
     */
    public function getActiveCategories(): Collection
    {
        return Category::active()
            ->orderBy('name')
            ->get();
    }
}
