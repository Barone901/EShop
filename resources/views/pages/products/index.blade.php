<x-layouts.default-layout title="Products">

    <div>

        {{-- Page Header --}}
        <x-ui.page-header
            title="All Products"
            subtitle="Browse our complete collection of products."
        />

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar - Filters --}}
            <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="card bg-white rounded-xl border p-4 sticky top-32">
                    <h3 class="font-semibold mb-4">Filters</h3>

                    {{-- Search --}}
                    <form action="{{ route('products.index') }}" method="GET" class="mb-6">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif

                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif

                        <div class="relative">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search products..."
                                aria-label="Search products"
                                class="w-full px-4 py-2 pr-10 border rounded-lg text-sm"
                            />
                            <button
                                type="submit"
                                aria-label="Search"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <svg class="w-5 h-5" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                            </button>
                        </div>
                    </form>

                    {{-- Categories --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-medium mb-3">Categories</h4>
                        <div class="space-y-2">
                            <a
                                href="{{ route('products.index', request()->except('category', 'page')) }}"
                                class="block text-sm py-1 px-2 rounded {{ !request('category') ? 'bg-black text-white' : 'text-gray-600 hover:bg-gray-100' }}"
                            >
                                All Categories
                            </a>

                            @foreach($categories as $category)
                                <a
                                    href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $category->slug])) }}"
                                    class="block text-sm py-1 px-2 rounded {{ request('category') === $category->slug ? 'bg-black text-white' : 'text-gray-600 hover:bg-gray-100' }}"
                                >
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Clear Filters --}}
                    @if(request()->hasAny(['category', 'search', 'sort']))
                        <a
                            href="{{ route('products.index') }}"
                            class="block text-center text-sm text-red-400 hover:text-red-300 py-2 border-t"
                        >
                            Clear All Filters
                        </a>
                    @endif
                </div>
            </aside>

            {{-- Products Grid --}}
            <div class="flex-1">
                {{-- Sort & Results Count --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <p class="text-sm text-gray-600">
                        Showing <span class="font-medium">{{ $products->firstItem() ?? 0 }}</span>
                        to <span class="font-medium">{{ $products->lastItem() ?? 0 }}</span>
                        of <span class="font-medium">{{ $products->total() }}</span> products
                    </p>

                    <div class="flex items-center gap-2">
                        <label for="sort" class="text-sm text-gray-600">Sort by:</label>
                        <select
                            id="sort"
                            data-navigate-on-change
                            class="px-3 py-2 border rounded-lg text-sm"
                        >
                            <option
                                value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}"
                                {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}
                            >
                                Newest
                            </option>
                            <option
                                value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'price_asc'])) }}"
                                {{ request('sort') === 'price_asc' ? 'selected' : '' }}
                            >
                                Price: Low to High
                            </option>
                            <option
                                value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'price_desc'])) }}"
                                {{ request('sort') === 'price_desc' ? 'selected' : '' }}
                            >
                                Price: High to Low
                            </option>
                            <option
                                value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'name'])) }}"
                                {{ request('sort') === 'name' ? 'selected' : '' }}
                            >
                                Name: A-Z
                            </option>
                        </select>
                    </div>
                </div>

                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <x-shop.product-card :product="$product" />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="card text-center py-16 bg-white rounded-xl border">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                            />
                        </svg>

                        <h3 class="text-lg font-semibold mb-2">No products found</h3>
                        <p class="text-gray-500 mb-6">Try adjusting your search or filter criteria.</p>

                        <a
                            href="{{ route('products.index') }}"
                            class="inline-flex items-center gap-2 bg-black text-white px-6 py-3 rounded-lg hover:opacity-90 transition-opacity"
                        >
                            View All Products
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.default-layout>
