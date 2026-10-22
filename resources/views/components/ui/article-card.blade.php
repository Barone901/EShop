@props(['page'])

<article class="article-card card bg-white rounded-lg shadow-md overflow-hidden">
    @if($page->featured_image)
        <a href="{{ route('pages.show', $page->slug) }}">
            <img
                src="{{ $page->featured_image_url }}"
                alt="{{ $page->title }}"
                class="w-full h-48 object-cover"
            >
        </a>
    @else
        <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
            <svg class="w-16 h-16 text-white/50" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
    @endif

    <div class="p-5">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            @if($page->published_at)
                <time datetime="{{ $page->published_at->toDateString() }}">
                    {{ $page->published_at->format('d.m.Y') }}
                </time>
            @endif
            @if($page->is_featured)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                    Recommended
                </span>
            @endif
        </div>

        <h2 class="text-xl font-semibold text-gray-900 mb-2">
            <a href="{{ route('pages.show', $page->slug) }}" class="hover:text-indigo-400 transition-colors">
                {{ $page->title }}
            </a>
        </h2>

        @if($page->excerpt)
            <p class="text-gray-600 text-sm line-clamp-3">{{ $page->excerpt }}</p>
        @endif

        <div class="mt-4">
            <a href="{{ route('pages.show', $page->slug) }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium inline-flex items-center gap-1">
                Read more
                <svg class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</article>

