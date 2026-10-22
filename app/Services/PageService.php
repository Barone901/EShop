<?php

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
     * Najde publikovanu stranku podla slug.
     */
    public function findBySlug(string $slug): ?Page
    {
        return Page::where('slug', $slug)
            ->published()
            ->first();
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
