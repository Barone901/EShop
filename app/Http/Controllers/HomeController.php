<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\View\View;

/**
 * Controller pre hlavnu stranku eshopu.
 */
class HomeController extends Controller
{
    /**
     * Injektuje sluzbu pre kategorie.
     */
    public function __construct(
        private CategoryService $categoryService
    ) {}

    /**
     * Zobrazi hlavnu stranku s kategoriami.
     */
    public function index(): View
    {
        $categories = $this->categoryService->getParentCategories();

        return view('pages.home', compact('categories'));
    }
}
