{{-- Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene. --}}
{{-- pattern = vzor z App\Support\InputPattern, doplni aj hlasku pre JS validator (ako x-form.input) --}}
{{-- Okraj, focus a chybovy stav (aria-invalid) riesi app.css, preto tu nie su farby okraja. --}}
@props(['disabled' => false, 'pattern' => null])

@php
    if ($pattern) {
        $attributes = $attributes->merge([
            'pattern' => $pattern,
            'data-pattern-message' => \App\Support\InputPattern::message($pattern),
        ]);
    }

    // Chyba zo servera (predvoleny error bag) -> rovnaky chybovy stav ako pri JS validacii
    $name = $attributes->get('name');
    if ($name && $errors->has($name)) {
        $attributes = $attributes->merge(['aria-invalid' => 'true']);
    }
@endphp

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full min-h-11 px-3 py-2.5 text-base disabled:bg-tile disabled:text-subtle']) }}>
