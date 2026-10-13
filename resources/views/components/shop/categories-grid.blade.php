@props(['categories'])

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-5">
    @foreach($categories as $category)
        <a
            href="{{ route('products.index', ['category' => $category->slug]) }}"
            class="card group rounded-xl border bg-white overflow-hidden hover:shadow-md transition-shadow"
        >
            <div class="aspect-[4/3] bg-gray-50 overflow-hidden">
                @if($category->image_src)
                    <img
                        src="{{ $category->image_src }}"
                        alt="{{ $category->name }}"
                        class="h-full w-full object-cover group-hover:scale-[1.03] transition-transform duration-300"
                        loading="lazy"
                    >
                @else
                    <div class="h-full w-full flex items-center justify-center text-gray-400 text-sm">
                        No image
                    </div>
                @endif
            </div>

            <div class="p-3">
                <div class="font-semibold text-sm sm:text-base leading-tight line-clamp-2">
                    {{ $category->name }}
                </div>

                {{-- jemný „call to action“ --}}
                <div class="mt-2 text-xs text-gray-600 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1">
                        View
                        <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
            </div>
        </a>
    @endforeach
</div>
