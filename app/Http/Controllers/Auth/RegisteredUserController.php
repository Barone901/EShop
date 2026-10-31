<?php

// Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene.

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
        return view('auth.register', [
            'patterns' => RegisterRequest::PATTERNS,
        ]);
    }

    /**
     * Spracuje registraciu, vytvori pouzivatela a priradi rolu customer.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Heslo zahesuje cast 'hashed' v modeli User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->assignRole(UserRole::Customer->value);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
