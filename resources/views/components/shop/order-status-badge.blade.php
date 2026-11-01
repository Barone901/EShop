{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Stav objednavky. Farbu urcuje OrderStatus::badgeColor() (nazov farby, pouziva sa aj inde),
    tu sa mapuje na varianty x-ui.badge dizajnoveho systemu.
    size: xs | sm -> maly stitok, lg -> vacsi (detail objednavky)
--}}
@props(['order', 'size' => 'xs'])

@php
    /** @var \App\Models\Order $order */
    /** @var string $size */

    $variant = match ($order->status->badgeColor()) {
        // Cervenu (danger) ma len zrusena objednavka - accent (brand cervena) sa tu nepouziva, aby sa neplietli
        'yellow' => 'warn',
        'blue', 'indigo' => 'neutral',
        'purple' => 'dark',
        'green' => 'ok',
        'red' => 'danger',
        default => 'neutral',
    };
@endphp

<x-ui.badge :variant="$variant" :size="$size === 'lg' ? 'lg' : 'sm'" dot {{ $attributes }}>
    {{ $order->status->getLabel() }}
</x-ui.badge>
