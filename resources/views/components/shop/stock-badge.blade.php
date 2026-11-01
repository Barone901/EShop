{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{-- Dostupnost produktu (text + farba + bodka). --}}
@props(['product'])

@if(!$product->is_in_stock)
    <x-ui.badge variant="danger" dot {{ $attributes }}>Out of Stock</x-ui.badge>
@elseif($product->is_low_stock)
    <x-ui.badge variant="warn" dot {{ $attributes }}>Last items: {{ $product->stock }}</x-ui.badge>
@else
    <x-ui.badge variant="ok" dot {{ $attributes }}>In Stock: {{ $product->stock }}</x-ui.badge>
@endif
