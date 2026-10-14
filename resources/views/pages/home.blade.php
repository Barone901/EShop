<x-layouts.default-layout title="Home">

    {{-- KATALÓG KATEGÓRIÍ --}}
    <section>
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight">Product categories</h2>
            </div>

            <a href="{{ route('products.index') }}"
               class="hidden sm:inline-flex items-center rounded-lg border px-4 py-2 text-sm hover:bg-gray-50">
                View all products
            </a>
        </div>

        <div class="mt-6">
            <x-shop.categories-grid :categories="$categories" />
        </div>
    </section>

</x-layouts.default-layout>
