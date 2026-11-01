{{-- Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene. --}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-text']) }}>
    {{ $value ?? $slot }}
</label>
