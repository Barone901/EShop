<?php

// Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene.

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller pre spravu profilu pouzivatela.
 */
class ProfileController extends Controller
{
    /**
     * Presmeruje na dashboard s kotovov na sekciu profilu.
     */
    public function edit(): RedirectResponse
    {
        return redirect()->route('dashboard')->withFragment('profile');
    }

    /**
     * Aktualizuje profil pouzivatela a resetuje verifikaciu emailu pri zmene.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('dashboard')
            ->with('status', 'profile-updated')
            ->withFragment('profile');
    }

    /**
     * Zmaze ucet pouzivatela po overeni hesla.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
