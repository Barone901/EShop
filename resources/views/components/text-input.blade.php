{{-- Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene. --}}
{{-- pattern = vzor z App\Support\InputPattern, doplni aj hlasku pre JS validator (ako x-form.input) --}}
@props(['disabled' => false, 'pattern' => null])

@php
    if ($pattern) {
        $attributes = $attributes->merge([
            'pattern' => $pattern,
            'data-pattern-message' => \App\Support\InputPattern::message($pattern),
        ]);
    }
@endphp

<input @disabled($disabled) {{ $attributes->merge(['class' => 'px-4 py-3 border border-gray-300 rounded-lg']) }}>
