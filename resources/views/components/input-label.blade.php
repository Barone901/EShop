{{-- Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene. --}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>
