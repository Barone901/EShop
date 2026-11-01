{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Karta produktu vo vypise.
    JS: [data-quick-view] (quickview.js) a [data-add-to-cart] (cart.js) - atributy nemen.
    Kategoria sa zobrazi len ak je relacia nacitana (bez dalsich dotazov do DB).
--}}
@props(['product'])

@php
    $categoryName = $product->relationLoaded('category') ? $product->category?->name : null;
@endphp

<article class="product-card card group flex flex-col">
    {{-- Fotka na svetlej dlazdici (produktove fotky byvaju na bielom pozadi) --}}
    <div class="relative aspect-[4/3] border-b border-line bg-tile">
        <a href="{{ route('product.show', $product) }}" class="block h-full" tabindex="-1" aria-hidden="true">
            @if($product->image_src)
                <img
                    src="{{ $product->image_src }}"
                    alt=""
                    loading="lazy"
                    class="h-full w-full object-contain p-4 mix-blend-multiply transition-transform duration-300 group-hover:scale-[1.03] motion-reduce:transition-none"
                />
            @else
                <span class="flex h-full items-center justify-center text-line-strong">
                    <svg class="size-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="square" stroke-width="1.5" d="M4 16l4.6-4.6a2 2 0 012.8 0L16 16m-2-2l1.6-1.6a2 2 0 012.8 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
            @endif
        </a>

        {{-- Rychly nahlad: vzdy viditelny (aj na dotykovych zariadeniach) --}}
        <button
            type="button"
            data-quick-view="{{ $product->slug }}"
            aria-label="Quick view: {{ $product->name }}"
            class="absolute right-2 top-2 grid size-10 place-items-center rounded-xs border border-line bg-white text-muted transition-colors hover:border-brand-600 hover:text-brand-600"
        >
            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </button>
    </div>

    <div class="flex flex-1 flex-col p-4">
        <p class="eyebrow truncate">
            @if($categoryName){{ $categoryName }} · @endif No. {{ str_pad((string) $product->id, 5, '0', STR_PAD_LEFT) }}
        </p>

        <h3 class="mt-1.5 font-sans text-base font-semibold normal-case tracking-normal leading-snug">
            <a href="{{ route('product.show', $product) }}" class="line-clamp-2 hover:text-brand-600">{{ $product->name }}</a>
        </h3>

        @if($product->description)
            <p class="mt-1.5 line-clamp-2 text-sm text-muted">{{ $product->description }}</p>
        @endif

        <div class="mt-3">
            <x-shop.stock-badge :product="$product" />
        </div>

        <div class="mt-auto pt-4">
            <p class="num text-xl text-text">{{ $product->formatted_price }}</p>

            @if($product->is_in_stock)
                <x-button type="button" class="mt-3 w-full" data-add-to-cart="{{ $product->id }}">
                    Add to Cart
                </x-button>
            @else
                <x-button variant="secondary" class="mt-3 w-full" disabled>
                    Unavailable
                </x-button>
            @endif
        </div>
    </div>
</article>
