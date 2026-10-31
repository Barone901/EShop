<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
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

        // API odpovede (resources/js) ocakavaju data priamo, nie zabalene v kluci "data"
        JsonResource::withoutWrapping();

        // Mimo produkcie zalogujeme lazy loading relacii v cykle (N+1 dotazy)
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            Log::warning('Lazy loading [' . $relation . '] on model [' . $model::class . '].');
        });
    }
}
