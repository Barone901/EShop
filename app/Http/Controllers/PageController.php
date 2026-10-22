<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PageService;
use Illuminate\View\View;

/**
 * Controller pre clanky a staticke stranky.
 */
class PageController extends Controller
{
    /**
     * Injektuje sluzbu pre stranky.
     */
    public function __construct(
        private PageService $pageService
    ) {}

    /**
     * Zobrazi zoznam publikovanych clankov.
     */
    public function index(): View
    {
        $pages = $this->pageService->getPublishedPages();

        return view('pages.articles.index', compact('pages'));
    }

    /**
     * Zobrazi detail clanku podla slug a zvysi pocitadlo zobrazeni.
     */
    public function show(string $slug): View
    {
        $page = $this->pageService->findBySlug($slug);

        if (!$page) {
            abort(404);
        }

        $page->incrementViews();

        $relatedPages = $this->pageService->getRelatedPages($page);

        return view('pages.articles.show', compact('page', 'relatedPages'));
    }
}
