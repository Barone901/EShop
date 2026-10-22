<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\BannerService;
use App\Services\CategoryService;
use Illuminate\View\View;

/**
 * Controller pre pracu s kategoriami produktov.
 */
class CategoryController extends Controller
{
    /**
     * Injektuje sluzby pre kategorie a bannery.
     */
    public function __construct(
        private CategoryService $categoryService,
        private BannerService $bannerService
    ) {}

    /**
     * Zobrazi stranku kategorie (rovnaky prehlad kategorii ako homepage).
     */
    public function show(Category $category): View
    {
        abort_unless($category->is_active, 404);

        $categories = $this->categoryService->getParentCategories();
        $banners = $this->bannerService->getActiveBanners();

        return view('pages.home', compact('categories', 'banners', 'category'));
    }
}
