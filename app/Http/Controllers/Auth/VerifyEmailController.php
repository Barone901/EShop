<?php

// [AI] Vygenerovane s pomocou AI (Claude) podla Laravel Breeze (https://github.com/laravel/breeze).

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

/**
 * Controller pre overenie emailu cez podpisany odkaz.
 */
class VerifyEmailController extends Controller
{
    /**
     * Oznaci email pouzivatela ako overeny a presmeruje na dashboard.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('dashboard');
    }
}
