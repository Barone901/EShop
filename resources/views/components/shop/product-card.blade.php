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

            {{-- Quick view button --}}
            <button
                type="button"
                data-quick-view="{{ $product->slug }}"
                aria-label="Quick view: {{ $product->name }}"
                class="absolute top-2 right-2 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm hover:bg-white"
            >
                <svg class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>

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
