{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Tlacidlo dizajnoveho systemu (nahradza Breeze primary/secondary/danger-button).
    - variant: primary | secondary | ghost | danger (obrysove, tmava cervena) | dark (obrysove na tmavom pozadi)
    - size: sm | md | lg (md a lg maju min. vysku 44px pre dotyk)
    - href: ak je zadane, vykresli sa <a> namiesto <button>
    Ostatne atributy (type, data-*, x-on:*, id...) sa predavaju priamo elementu.
--}}
@props(['variant' => 'primary', 'size' => 'md', 'href' => null])

@php
$base = 'inline-flex items-center justify-center gap-2 rounded-xs border font-display font-semibold uppercase tracking-wider '
    . 'transition-colors duration-150 disabled:cursor-not-allowed disabled:border-transparent disabled:bg-line disabled:text-subtle';

$sizeClasses = match($size) {
    'sm' => 'min-h-9 px-3 text-sm',
    'lg' => 'min-h-12 px-6 text-lg',
    default => 'min-h-11 px-5 text-base',
};

$variantClasses = match($variant) {
    'secondary' => 'border-line-strong bg-white text-text hover:border-text',
    'ghost' => 'border-transparent bg-transparent text-muted hover:bg-tile hover:text-text',
    // Nebezpecna akcia: obrysove v tmavej cervenej, aby sa neplietla s primarnym (brand) tlacidlom
    'danger' => 'border-danger-700 bg-white text-danger-700 hover:bg-danger-700 hover:text-white',
    'dark' => 'border-steel-100/60 bg-transparent text-steel-100 hover:border-steel-100 hover:bg-white/5',
    default => 'border-transparent bg-brand-600 text-white hover:bg-brand-700',
};

$classes = "{$base} {$sizeClasses} {$variantClasses}";

// Predvolene odosiela formular len primarne a nebezpecne tlacidlo (ako v Breeze)
$defaultType = in_array($variant, ['primary', 'danger'], true) ? 'submit' : 'button';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => $defaultType, 'class' => $classes]) }}>{{ $slot }}</button>
@endif
