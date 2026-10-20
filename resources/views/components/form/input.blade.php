{{--
    [AI] Vygenerovane s pomocou AI (Claude) - vyclenene z opakujucich sa poli vo formularoch.
    Formularove pole: label + input/textarea + chybova hlaska.
    - id pola = name, hodnota = old(name, value)
    - povinne pole ma v labeli hviezdicku
    - trieda (class) sa aplikuje na obal, ostatne atributy (placeholder, autocomplete...) na pole
    - inputClass = doplnkove triedy priamo pre pole (napr. resize-none)
    - focus stav je jednotny pre vsetky polia (app.css)
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
])

@php
    // Pri chybe cerveny okraj namiesto sedeho (border-gray-* prefarbuje app.css, preto nie oba naraz)
    $borderClass = $errors->has($name) ? 'border-red-500' : 'border-gray-300';
    $fieldClasses = trim("w-full px-4 py-3 border {$borderClass} rounded-lg {$inputClass}");
@endphp

<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}@if($required) *@endif</label>

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
        <p class="mt-1 text-sm text-red-400 validation-error">{{ $message }}</p>
    @enderror
</div>
