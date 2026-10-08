<?php

// Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene.

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Controller pre opakovane odoslanie verifikacneho emailu.
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * Posle znova verifikacny email ak este nie je overeny.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
