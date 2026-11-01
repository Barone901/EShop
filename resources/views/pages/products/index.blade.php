{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Vypis produktov s filtrom kategorie, vyhladavanim a radenim (GET parametre category, search, sort).
    JS: select #sort [data-navigate-on-change] (page-actions.js) - hodnota option = URL.
--}}
@php
    $activeCategory = $categories->firstWhere('slug', request('category'));
    $search = request('search');

    // Odkazy kategorii (rovnake pre sidebar aj mobilne "chipy")
    $categoryLinks = collect([['All Categories', route('products.index', request()->except('category', 'page')), !request('category')]])
        ->concat($categories->map(fn ($category) => [
            $category->name,
            route('products.index', array_merge(request()->except('page'), ['category' => $category->slug])),
            request('category') === $category->slug,
        ]));

    $sorts = [
        'newest' => 'Newest',
        'price_asc' => 'Price: Low to High',
        'price_desc' => 'Price: High to Low',
        'name' => 'Name: A-Z',
    ];
@endphp

<x-layouts.default-layout title="Products">

    <x-ui.page-header
        eyebrow="Catalog"
        :title="$activeCategory?->name ?? 'All Products'"
        subtitle="Browse our complete collection of products."
    />

    <div class="flex flex-col gap-6 lg:flex-row lg:gap-8">
        {{-- Sidebar: kategorie (desktop) --}}
        <aside class="hidden w-64 shrink-0 lg:block" aria-label="Categories">
            <nav class="card sticky top-36">
                <h2 class="border-b border-line px-4 py-3 text-lg">Categories</h2>
                <ul class="py-2">
                    @foreach($categoryLinks as [$label, $url, $active])
                        <li>
                            <a href="{{ $url }}"
                               @if($active) aria-current="page" @endif
                               @class([
                                   'flex min-h-10 items-center border-l-3 px-4 text-sm transition-colors',
                                   'border-brand-600 bg-brand-50 font-semibold text-brand-600' => $active,
                                   'border-transparent text-muted hover:bg-tile hover:text-text' => !$active,
                               ])>{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>

        <div class="min-w-0 flex-1">
            {{-- Kategorie (mobil): vodorovne posuvne "chipy" --}}
            <nav class="-mx-4 mb-4 overflow-x-auto px-4 lg:hidden" aria-label="Categories">
                <ul class="flex gap-2 pb-1">
                    @foreach($categoryLinks as [$label, $url, $active])
                        <li class="shrink-0">
                            <a href="{{ $url }}"
                               @if($active) aria-current="page" @endif
                               @class([
                                   'inline-flex min-h-10 items-center rounded-xs border px-3 text-sm whitespace-nowrap',
                                   'border-brand-600 bg-brand-50 font-semibold text-brand-600' => $active,
                                   'border-line bg-white text-muted hover:border-line-strong hover:text-text' => !$active,
                               ])>{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            {{-- Pocet vysledkov + aktivne filtre + radenie (vyhladavanie je v hlavicke) --}}
            <div class="mb-6 flex flex-col gap-3 border-b border-line pb-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <p class="text-sm text-muted" role="status">
                        Showing <span class="num text-text">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</span>
                        of <span class="num text-text">{{ $products->total() }}</span> products
                    </p>

                    @if($search)
                        <a href="{{ route('products.index', request()->except('search', 'page')) }}"
                           class="inline-flex min-h-8 items-center gap-1.5 rounded-xs border border-line bg-white px-2.5 text-xs hover:border-danger-700 hover:text-danger-700"
                           aria-label="Remove search filter: {{ $search }}">
                            Search: <span class="font-semibold">{{ $search }}</span>
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif

                    @if(request()->hasAny(['category', 'search', 'sort']))
                        <a href="{{ route('products.index') }}" class="text-sm font-medium text-brand-600 underline-offset-4 hover:underline">
                            Clear all filters
                        </a>
                    @endif
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <label for="sort" class="shrink-0 text-sm text-muted">Sort by</label>
                    <select id="sort" data-navigate-on-change class="block min-h-11 w-full py-2 pl-3 pr-9 text-sm sm:w-auto">
                        @foreach($sorts as $value => $label)
                            <option
                                value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => $value])) }}"
                                @selected(request('sort', 'newest') === $value)
                            >{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 xl:grid-cols-3">
                    @foreach($products as $product)
                        <x-shop.product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $products->links('pagination.shop') }}
                </div>
            @else
                <x-ui.card padding="lg" class="text-center">
                    <svg class="mx-auto mb-4 size-14 text-line-strong" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="square" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h2 class="text-2xl">No products found</h2>
                    <p class="mx-auto mt-2 max-w-sm text-muted">Try adjusting your search or filter criteria.</p>
                    <x-button :href="route('products.index')" class="mt-6">View all products</x-button>
                </x-ui.card>
            @endif
        </div>
    </div>
</x-layouts.default-layout>
