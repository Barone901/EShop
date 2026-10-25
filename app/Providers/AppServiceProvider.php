<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Policies (App\Policies\{Model}Policy) aj Blade komponent <x-layouts.default-layout>
     * Laravel najde automaticky podla nazvu, netreba ich registrovat rucne.
     */
    public function boot(): void
    {
        // Super admin ma pristup ku vsetkemu - okrem adries zakaznikov, tie patria len vlastnikovi
        Gate::before(function (User $user, string $ability, array $arguments) {
            if (($arguments[0] ?? null) instanceof UserAddress) {
                return null;
            }

            return $user->hasRole('admin') ? true : null;
        });
    }
}
