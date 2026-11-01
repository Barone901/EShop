{{-- [AI] Vygenerovane s pomocou AI (Claude) pri redizajne. --}}
{{--
    Biela karta/panel v obsahovej casti.
    - padding: none | sm | md | lg
    - interactive: pri hoveri okraj v akcentovej farbe (klikatelne karty)
--}}
@props(['padding' => 'md', 'interactive' => false])

@php
$paddingClasses = match($padding) {
    'none' => '',
    'sm' => 'p-4',
    'lg' => 'p-6 md:p-8',
    default => 'p-5 md:p-6',
};
@endphp

<div {{ $attributes->class(['card', $paddingClasses, 'transition-colors hover:border-brand-600' => $interactive]) }}>
    {{ $slot }}
</div>
