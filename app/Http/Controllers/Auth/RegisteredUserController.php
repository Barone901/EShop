<?php

// Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene.

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Controller pre registraciu novych pouzivatelov.
 */
class RegisteredUserController extends Controller
{
    /**
     * Zobrazi registracny formular.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Spracuje registraciu a prihlasi noveho pouzivatela.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make((string) $validated['password']),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
