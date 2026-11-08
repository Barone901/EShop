{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{-- Dostupnost produktu (text + farba + bodka). --}}
@props(['product'])

@if(!$product->is_in_stock)
    <x-ui.badge variant="danger" dot {{ $attributes }}>{{ __('Out of Stock') }}</x-ui.badge>
@elseif($product->is_low_stock)
    <x-ui.badge variant="warn" dot {{ $attributes }}>{{ __('Last items: :count', ['count' => $product->stock]) }}</x-ui.badge>
@else
    <x-ui.badge variant="ok" dot {{ $attributes }}>{{ __('In Stock: :count', ['count' => $product->stock]) }}</x-ui.badge>
@endif
