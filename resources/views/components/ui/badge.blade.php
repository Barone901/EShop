{{-- [AI] Vygenerovane s pomocou AI (Claude) pri redizajne. --}}
{{--
    Stitok stavu. Farba nikdy nie je jediny nositel informacie - vzdy je tam aj text.
    - variant: neutral | ok | warn | danger | accent | dark
    - size: sm (predvolene) | lg
    - dot: farebna bodka pred textom
--}}
@props(['variant' => 'neutral', 'size' => 'sm', 'dot' => false])

@php
$variantClasses = match($variant) {
    'ok' => 'bg-ok-50 text-ok-700',
    'warn' => 'bg-warn-50 text-warn-700',
    'danger' => 'bg-danger-50 text-danger-700',
    'accent' => 'bg-brand-50 text-brand-600',
    'dark' => 'bg-ink-900 text-steel-100',
    default => 'bg-tile text-muted',
};

$sizeClasses = $size === 'lg' ? 'px-3 py-1.5 text-sm' : 'px-2 py-1 text-xs';
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-xs font-mono font-medium uppercase tracking-wider', $sizeClasses, $variantClasses]) }}>
    @if($dot)
        <span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
