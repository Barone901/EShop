<?php

// Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene.

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller pre zobrazenie vyzvy na overenie emailu.
 */
class EmailVerificationPromptController extends Controller
{
    /**
     * Zobrazi stranku s vyzvou na overenie emailu alebo presmeruje ak je overeny.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(route('dashboard', absolute: false))
            : view('auth.verify-email');
    }
}
