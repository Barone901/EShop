<?php

// [AI] Upravene s pomocou AI (Claude): jednotny styl (readonly injekcia, data do view polom).

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BannerService;
use App\Services\CategoryService;
use Illuminate\View\View;

/**
 * Controller pre hlavnu stranku eshopu.
 */
class HomeController extends Controller
{
    /**
     * Injektuje sluzby pre kategorie a bannery.
     */
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly BannerService $bannerService
    ) {}

    /**
     * Zobrazi hlavnu stranku s kategoriami a bannermi.
     */
    public function index(): View
    {
        return view('pages.home', [
            'categories' => $this->categoryService->getParentCategories(),
            'banners' => $this->bannerService->getActiveBanners(),
        ]);
    }
}
