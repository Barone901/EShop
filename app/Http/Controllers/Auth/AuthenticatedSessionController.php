<?php

// Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene.

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller pre prihlasovanie a odhlasovanie pouzivatelov.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Zobrazi prihlasovaci formular.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Spracuje prihlasenie a zregeneruje session pre bezpecnost.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Odhlasi pouzivatela a zneplatni session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
