<?php

// [AI] Upravene s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\Request;
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
        private readonly PageService $pageService
    ) {}

    /**
     * Zobrazi zoznam publikovanych clankov.
     */
    public function index(): View
    {
        return view('pages.articles.index', [
            'pages' => $this->pageService->getPublishedPages(),
        ]);
    }

    /**
     * Zobrazi detail clanku podla slug a zvysi pocitadlo zobrazeni.
     */
    public function show(Request $request, Page $page): View
    {
        // Clanok najde route model binding podla slugu; koncept alebo buduci clanok nie je verejny
        abort_unless($page->isPublished(), 404);

        // Zobrazenie pocitame raz za session, nie pri kazdom obnoveni stranky
        $viewedKey = "viewed_pages.{$page->id}";

        if (!$request->session()->has($viewedKey)) {
            $request->session()->put($viewedKey, true);
            $page->incrementViews();
        }

        return view('pages.articles.show', [
            'page' => $page,
            'relatedPages' => $this->pageService->getRelatedPages($page),
        ]);
    }
}
