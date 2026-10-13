@props(['product'])

@if(!$product->is_in_stock)
    <span class="rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700">
        Out of Stock
    </span>
@elseif($product->is_low_stock)
    <span class="rounded-full bg-orange-50 px-3 py-1 text-sm font-medium text-orange-700">
        Last items: {{ $product->stock }}
    </span>
@else
    <span class="rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">
        In Stock: {{ $product->stock }}
    </span>
@endif
