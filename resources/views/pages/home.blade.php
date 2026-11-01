@php
    // Vyhody obchodu: len fakty, ktore aplikacia realne poskytuje
    $benefits = [
        ['Live stock levels', 'Availability is shown on every product.', 'M5 13l4 4L19 7'],
        ['Flat shipping ' . \App\Support\Price::format(config('shop.shipping_cost')), 'One shipping price for every order.', 'M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10h2m8 0H9m4 0h2m4 0h2v-5l-3-4h-5v9'],
        ['Phone support', '+421 912 345 678', 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
    ];

    $hasBanners = $banners && $banners->isNotEmpty();
@endphp

<x-layouts.default-layout title="Home">

    {{--
        UVOD (jeden blok): tmavy pas s rovnakym kontajnerom ako header/footer.
        Slider (8/12) + hero text (4/12); bez bannerov hero na celu sirku.
    --}}
    <x-slot name="fullWidth">
        <section class="theme-dark bg-ink-900 text-steel-100">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 md:py-8 lg:px-8">
                <div class="grid gap-6 lg:grid-cols-12 lg:gap-8">
                    @if($hasBanners)
                        <div class="lg:col-span-8">
                            <x-shop.banner-slider :banners="$banners" />
                        </div>
                    @endif

                    <div @class([
                        'flex flex-col justify-center border-l-3 border-brand-500 pl-5 md:pl-6',
                        'lg:col-span-4' => $hasBanners,
                        'lg:col-span-12 py-6' => !$hasBanners,
                    ])>
                        <p class="eyebrow text-brand-400">Triumf professional tools</p>
                        <h1 class="mt-3 text-3xl text-white md:text-4xl">Tools built for everyday work</h1>
                        <p class="mt-3 hidden text-steel-300 sm:block">
                            Hand tools, socket sets and workshop equipment for tradespeople and businesses.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Svetly pas s vyhodami (informacia, nie druhy uvod) --}}
        <section class="border-b border-line bg-white" aria-label="Why shop with us">
            <ul class="mx-auto grid max-w-7xl grid-cols-2 gap-x-4 gap-y-5 px-4 py-6 sm:px-6 lg:grid-cols-4 lg:gap-8 lg:px-8">
                @foreach($benefits as [$heading, $text, $iconPath])
                    <li class="flex items-start gap-3">
                        <span class="grid size-10 shrink-0 place-items-center rounded-xs bg-brand-50 text-brand-600" aria-hidden="true">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="square" stroke-width="2" d="{{ $iconPath }}"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="font-display text-base font-semibold uppercase leading-tight tracking-wide">{{ $heading }}</p>
                            <p class="mt-0.5 text-sm text-muted">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </x-slot>

    {{-- KATALOG KATEGORII (svetla zona, rovnaky kontajner) --}}
    <section aria-labelledby="home-categories">
        <x-ui.section-header
            heading-id="home-categories"
            title="Product categories"
            :href="route('products.index')"
            linkText="View all products"
        />

        <x-shop.categories-grid :categories="$categories" />
    </section>

</x-layouts.default-layout>
