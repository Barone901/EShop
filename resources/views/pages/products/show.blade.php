<x-layouts.default-layout>

    <div>
        <div class="grid gap-8 lg:grid-cols-2">

            {{-- Image --}}
            <div class="card overflow-hidden rounded-xl border bg-white">
                @if($product->image_src)
                    <img
                        src="{{ $product->image_src }}"
                        alt="{{ $product->name }}"
                        class="h-full w-full object-cover"
                    />
                @endif
            </div>

            {{-- Info --}}
            <div>
                <div class="text-sm text-gray-500">
                    Category:
                    <span class="font-medium text-gray-900">
                        {{ $product->category?->name ?? '—' }}
                    </span>
                </div>

                <h1 class="mt-2 text-3xl font-bold tracking-tight">{{ $product->name }}</h1>

                <div class="mt-4 flex items-center gap-4">
                    <div class="text-2xl font-bold">
                        {{ $product->formatted_price }}
                    </div>

                    <x-shop.stock-badge :product="$product" />
                </div>

                <div class="mt-6 text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $product->description }}
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="rounded-lg border px-5 py-3 text-sm hover:bg-gray-50">
                        Back
                    </a>
                </div>
            </div>
        </div>

    </div>

</x-layouts.default-layout>
