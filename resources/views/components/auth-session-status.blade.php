{{-- Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene. --}}
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
