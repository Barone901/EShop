<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
     *
     * Rola admin nema ziadnu vynimku (Gate::before) - ma vsetky opravnenia
     * (RolesAndPermissionsSeeder), takze plati aj to, co policy zakazuje vsetkym
     * (napr. vytvorenie objednavky v admine) a cudzie adresy zakaznikov.
     */
    public function boot(): void
    {
        // Jednotne pravidla hesla pre registraciu aj zmenu hesla v profile
        // (rovnake pravidla kontroluje v prehliadaci form-validator.js, pravidlo "password")
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers());

        // Pomenovane limity poziadaviek (pouzivaju ich routy: throttle:contact, throttle:shop-api, throttle:verification)
        RateLimiter::for('contact', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('shop-api', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('verification', fn (Request $request) => Limit::perMinute(6)->by($request->user()?->id ?: $request->ip()));

        // Mimo produkcie zalogujeme lazy loading relacii v cykle (N+1 dotazy)
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            Log::warning('Lazy loading [' . $relation . '] on model [' . $model::class . '].');
        });
    }
}
