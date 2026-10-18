@props(['product'])

<div class="product-card group overflow-hidden rounded-xl border bg-white">
    <a href="{{ route('product.show', $product) }}" class="block">
        <div class="aspect-[4/3] bg-gray-100 relative overflow-hidden">
            @if($product->image_src)
                <img
                    src="{{ $product->image_src }}"
                    alt="{{ $product->name }}"
                    class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
            @endif

            {{-- Stock badge --}}
            @if(!$product->is_in_stock)
                <span class="absolute bottom-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                    Out of Stock
                </span>
            @elseif($product->is_low_stock)
                <span class="absolute bottom-2 left-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full">
                    Last items
                </span>
            @endif
        </div>
    </a>

    <div class="p-4">
        <a href="{{ route('product.show', $product) }}">
            <h3 class="font-semibold group-hover:underline">{{ $product->name }}</h3>
        </a>
        <p class="mt-1 text-sm text-gray-600 line-clamp-2">
            {{ $product->description }}
        </p>

        <div class="mt-4 flex items-center justify-between">
            <span class="font-bold">{{ $product->formatted_price }}</span>
            @if($product->is_in_stock)
                <button
                    type="button"
                    data-add-to-cart="{{ $product->id }}"
                    class="rounded-lg bg-black text-white px-3 py-2 text-sm hover:opacity-90 transition-opacity"
                >
                    Add to Cart
                </button>
            @else
                <span class="text-sm text-gray-400">Unavailable</span>
            @endif
        </div>
    </div>
</div>
