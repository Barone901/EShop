<?php

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
        private CategoryService $categoryService,
        private BannerService $bannerService
    ) {}

    /**
     * Zobrazi hlavnu stranku s kategoriami a bannermi.
     */
    public function index(): View
    {
        $categories = $this->categoryService->getParentCategories();
        $banners = $this->bannerService->getActiveBanners();

        return view('pages.home', compact('categories', 'banners'));
    }
}
