{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
{{--
    [AI] Vygenerovane s pomocou AI (Claude) - vyclenene z opakujucich sa poli vo formularoch.
    Formularove pole: label + input/textarea + chybova hlaska.
    - id pola = name, hodnota = old(name, value)
    - povinne pole ma v labeli hviezdicku
    - trieda (class) sa aplikuje na obal, ostatne atributy (placeholder, autocomplete...) na pole
    - inputClass = doplnkove triedy priamo pre pole (napr. resize-none)
    - okraj, focus a chybovy stav su jednotne pre vsetky polia (app.css); chyba zo servera nastavi aria-invalid
    - pattern = vzor z App\Support\InputPattern (rovnaky ako na serveri), doplni aj hlasku pre JS validator
    - maxlength, minlength a dalsie HTML atributy sa predavaju priamo poli
--}}
@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'textarea' => false,
    'inputClass' => '',
    'pattern' => null,
])

@php
    $fieldClasses = trim("block w-full min-h-11 px-3 py-2.5 text-base {$inputClass}");

    // Chyba zo servera -> rovnaky chybovy stav ako pri JS validacii (form-validator.js)
    if ($errors->has($name)) {
        $attributes = $attributes->merge(['aria-invalid' => 'true']);
    }

    if ($pattern) {
        $attributes = $attributes->merge([
            'pattern' => $pattern,
            'data-pattern-message' => \App\Support\InputPattern::message($pattern),
        ]);
    }
@endphp

<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-text">{{ $label }}@if($required) <span class="text-brand-600" aria-hidden="true">*</span>@endif</label>

    @if($textarea)
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            @required($required)
            {{ $attributes->except('class')->class($fieldClasses) }}
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            @required($required)
            {{ $attributes->except('class')->class($fieldClasses) }}
        >
    @endif

    @error($name)
        {{-- validation-error: JS validator hlasku zo servera odstrani, ked pouzivatel pole opravi --}}
        <p class="validation-error">{{ $message }}</p>
    @enderror
</div>
