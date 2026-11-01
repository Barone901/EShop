{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Detail produktu.
    JS: [data-add-to-cart] + data-quantity-input="#quantity" (cart.js) cita mnozstvo z inputu #quantity - nemen.

    Pripravene na rozsirenie (DB specs JSON + galeria je samostatna uloha):
    - $images: zoznam URL obrazkov; nahlady sa zobrazia az pri viac ako jednom obrazku
    - $specs: riadky tabulky parametrov (label => hodnota); technicke parametre sa sem neskor pridaju
--}}
@php
    $images = collect([$product->image_src])->filter()->values();

    $specs = [
        'Product No.' => str_pad((string) $product->id, 5, '0', STR_PAD_LEFT),
        'Category' => $product->category?->name ?? '—',
        'Availability' => $product->is_in_stock ? $product->stock . ' pcs in stock' : 'Out of stock',
        'Price' => $product->formatted_price,
    ];

    // Vyhody: len fakty, ktore aplikacia realne poskytuje (doprava z configu, firemne udaje v pokladni)
    $benefits = [
        ['Flat shipping ' . \App\Support\Price::format(config('shop.shipping_cost')), 'One shipping price for the whole order.', 'M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10h2m8 0H9m4 0h2m4 0h2v-5l-3-4h-5v9'],
        ['Statutory warranty', '24 months for consumers.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        ['Company invoicing', 'Company ID, tax ID and VAT ID at checkout.', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
    ];
@endphp

<x-layouts.default-layout :title="$product->name">

    {{-- Drobcekova navigacia --}}
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-subtle">
            <li><a href="{{ route('home') }}" class="hover:text-brand-600">Home</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('products.index') }}" class="hover:text-brand-600">Products</a></li>
            @if($product->category)
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-brand-600">{{ $product->category->name }}</a></li>
            @endif
            <li aria-hidden="true">/</li>
            <li aria-current="page" class="max-w-[16rem] truncate text-text">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">

        {{-- GALERIA --}}
        <div class="lg:col-span-7" x-data="{ active: 0 }">
            <div class="card relative aspect-square overflow-hidden bg-tile sm:aspect-[4/3]">
                @forelse($images as $index => $src)
                    <img
                        src="{{ $src }}"
                        alt="{{ $product->name }}{{ $images->count() > 1 ? ' – image ' . ($index + 1) : '' }}"
                        class="absolute inset-0 h-full w-full object-contain p-6 mix-blend-multiply md:p-10"
                        @if($images->count() > 1) x-show="active === {{ $index }}" @if($index > 0) x-cloak @endif @endif
                    />
                @empty
                    <span class="flex h-full items-center justify-center text-line-strong">
                        <svg class="size-20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="square" stroke-width="1" d="M4 16l4.6-4.6a2 2 0 012.8 0L16 16m-2-2l1.6-1.6a2 2 0 012.8 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="sr-only">No image available</span>
                    </span>
                @endforelse
            </div>

            {{-- Nahlady: len ak je viac obrazkov --}}
            @if($images->count() > 1)
                <ul class="mt-3 grid grid-cols-5 gap-2 sm:grid-cols-6">
                    @foreach($images as $index => $src)
                        <li>
                            <button
                                type="button"
                                x-on:click="active = {{ $index }}"
                                x-bind:aria-current="active === {{ $index }} ? 'true' : 'false'"
                                x-bind:class="active === {{ $index }} ? 'border-brand-600' : 'border-line hover:border-line-strong'"
                                class="block aspect-square w-full overflow-hidden rounded-xs border-2 bg-tile p-1"
                                aria-label="Show image {{ $index + 1 }}"
                            >
                                <img src="{{ $src }}" alt="" class="h-full w-full object-contain mix-blend-multiply" loading="lazy">
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- NAKUPNY PANEL --}}
        <div class="lg:col-span-5">
            <div class="lg:sticky lg:top-36">
                <p class="eyebrow">
                    {{ $product->category?->name ?? 'Product' }} · No. {{ $specs['Product No.'] }}
                </p>

                <h1 class="mt-2 text-3xl md:text-4xl">{{ $product->name }}</h1>

                <div class="mt-4">
                    <x-shop.stock-badge :product="$product" />
                </div>

                <div class="card mt-6 p-5">
                    <p class="num text-4xl text-text">{{ $product->formatted_price }}</p>
                    <p class="mt-1 text-sm text-subtle">
                        + shipping {{ \App\Support\Price::format(config('shop.shipping_cost')) }} per order
                    </p>

                    @if($product->is_in_stock)
                        <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                            {{-- Mnozstvo: +/- su nove (Alpine), cart.js cita len hodnotu #quantity --}}
                            <div class="flex shrink-0" x-data>
                                <label for="quantity" class="sr-only">Quantity</label>
                                <button
                                    type="button"
                                    x-on:click="$refs.qty.stepDown()"
                                    class="grid min-h-12 w-12 place-items-center rounded-l-xs border border-line-strong bg-white text-lg hover:bg-tile"
                                    aria-label="Decrease quantity"
                                >−</button>
                                <input
                                    type="number"
                                    id="quantity"
                                    x-ref="qty"
                                    value="1"
                                    min="1"
                                    max="{{ $product->stock }}"
                                    inputmode="numeric"
                                    class="num -mx-px block min-h-12 w-16 rounded-none text-center text-base"
                                />
                                <button
                                    type="button"
                                    x-on:click="$refs.qty.stepUp()"
                                    class="grid min-h-12 w-12 place-items-center rounded-r-xs border border-line-strong bg-white text-lg hover:bg-tile"
                                    aria-label="Increase quantity"
                                >+</button>
                            </div>

                            <x-button
                                type="button"
                                size="lg"
                                class="flex-1"
                                data-add-to-cart="{{ $product->id }}"
                                data-quantity-input="#quantity"
                            >
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="square" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Add to Cart
                            </x-button>
                        </div>
                    @else
                        <x-button variant="secondary" size="lg" class="mt-5 w-full" disabled>
                            Out of Stock
                        </x-button>
                    @endif
                </div>

                {{-- Doveryhodnostne prvky --}}
                <ul class="mt-6 divide-y divide-line border-y border-line">
                    @foreach($benefits as [$heading, $text, $iconPath])
                        <li class="flex gap-3 py-3">
                            <svg class="mt-0.5 size-5 shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="square" stroke-width="2" d="{{ $iconPath }}"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold">{{ $heading }}</p>
                                <p class="text-sm text-muted">{{ $text }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <x-button :href="route('products.index')" variant="ghost" class="mt-4 -ml-2">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="square" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to products
                </x-button>
            </div>
        </div>
    </div>

    {{-- POPIS + PARAMETRE --}}
    <div class="mt-12 grid gap-8 md:mt-16 lg:grid-cols-12 lg:gap-12">
        <section class="lg:col-span-7" aria-labelledby="product-description">
            <x-ui.section-header heading-id="product-description" eyebrow="01" title="Description" />
            @if($product->description)
                <div class="max-w-prose leading-relaxed whitespace-pre-line text-text">{{ $product->description }}</div>
            @else
                <p class="text-muted">No description available.</p>
            @endif
        </section>

        <section class="lg:col-span-5" aria-labelledby="product-specs">
            <x-ui.section-header heading-id="product-specs" eyebrow="02" title="Specifications" />
            <div class="card overflow-hidden">
                <table class="w-full text-sm">
                    <caption class="sr-only">Specifications of {{ $product->name }}</caption>
                    <tbody class="divide-y divide-line">
                        @foreach($specs as $label => $value)
                            <tr class="even:bg-paper">
                                <th scope="row" class="w-2/5 px-4 py-3 text-left font-medium text-muted">{{ $label }}</th>
                                <td class="num px-4 py-3 text-text">{{ $value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

</x-layouts.default-layout>
