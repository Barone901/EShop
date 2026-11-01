{{-- Karta clanku (vypis clankov aj suvisiace clanky). headingLevel = uroven nadpisu (h2 vo vypise, h3 pri suvisiacich). --}}
@props(['page', 'headingLevel' => 2])

@php $heading = 'h' . $headingLevel; @endphp

<article class="article-card card group flex h-full flex-col overflow-hidden">
    <a href="{{ route('pages.show', $page->slug) }}" class="block aspect-[16/9] overflow-hidden border-b border-line bg-tile" tabindex="-1" aria-hidden="true">
        @if($page->featured_image)
            <img
                src="{{ $page->featured_image_url }}"
                alt=""
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03] motion-reduce:transition-none"
            >
        @else
            <span class="flex h-full items-center justify-center text-line-strong">
                <svg class="size-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-5">
        <div class="mb-2 flex flex-wrap items-center gap-2">
            @if($page->published_at)
                <time class="eyebrow" datetime="{{ $page->published_at->toDateString() }}">{{ $page->published_at->format('d.m.Y') }}</time>
            @endif
            @if($page->is_featured)
                <x-ui.badge variant="dark">Recommended</x-ui.badge>
            @endif
        </div>

        <{{ $heading }} class="font-sans text-lg font-semibold normal-case leading-snug tracking-normal">
            <a href="{{ route('pages.show', $page->slug) }}" class="hover:text-brand-600">{{ $page->title }}</a>
        </{{ $heading }}>

        @if($page->excerpt)
            <p class="mt-2 line-clamp-3 text-sm text-muted">{{ $page->excerpt }}</p>
        @endif

        <a href="{{ route('pages.show', $page->slug) }}"
           class="mt-auto inline-flex min-h-11 items-center gap-1 pt-3 font-display text-base font-semibold uppercase tracking-wider text-brand-600 hover:text-brand-700"
           aria-label="Read more: {{ $page->title }}">
            Read more
            <svg class="size-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</article>
