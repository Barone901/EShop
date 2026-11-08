<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

declare(strict_types=1);

namespace App\Services;

use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Sluzba pre pracu s clankami a strankami.
 */
class PageService
{
    /**
     * Ziska strankovany zoznam publikovanych stranok.
     */
    public function getPublishedPages(int $perPage = 12): LengthAwarePaginator
    {
        return Page::published()
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * Ziska suvisiace stranky k danej stranke.
     */
    public function getRelatedPages(Page $page, int $limit = 3): Collection
    {
        $relatedPages = Page::published()
            ->where('id', '!=', $page->id)
            ->featured()
            ->limit($limit)
            ->get();

        if ($relatedPages->count() < $limit) {
            $additionalPages = Page::published()
                ->where('id', '!=', $page->id)
                ->whereNotIn('id', $relatedPages->pluck('id'))
                ->latest('published_at')
                ->limit($limit - $relatedPages->count())
                ->get();

            $relatedPages = $relatedPages->merge($additionalPages);
        }

        return $relatedPages;
    }
}
