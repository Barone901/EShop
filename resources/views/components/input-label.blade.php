{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
{{-- Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene. --}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-text']) }}>
    {{ $value ?? $slot }}
</label>
