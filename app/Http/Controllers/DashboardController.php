<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller pre prehlad uctu zakaznika.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('profile.dashboard', [
            'user' => $request->user(),
        ]);
    }
}
