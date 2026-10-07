{{-- Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene. --}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'px-4 py-3 border border-gray-300 rounded-lg']) }}>
