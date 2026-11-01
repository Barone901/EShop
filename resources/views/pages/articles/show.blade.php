{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
{{--
    Detail clanku. JS: [data-copy-url] (page-actions.js) - nemen.
    Obsah clanku je HTML z administracie (sanitizovane v modeli), styly v .article-content (app.css).
--}}
@php
    $title = $page->meta_title ?: $page->title;
    $description = $page->meta_description ?: $page->excerpt;
    $shareButton = 'inline-flex min-h-11 items-center gap-2 rounded-xs border border-line-strong bg-white px-4 text-sm font-medium text-text transition-colors hover:border-text';
@endphp

<x-layouts.default-layout :title="$title">
    <x-slot name="head">
        @if($description)
            <meta name="description" content="{{ Str::limit($description, 160) }}">
        @endif
        @if($page->meta_keywords)
            <meta name="keywords" content="{{ $page->meta_keywords }}">
        @endif
        {{-- Open Graph --}}
        <meta property="og:title" content="{{ $title }}">
        <meta property="og:type" content="article">
        <meta property="og:url" content="{{ url()->current() }}">
        @if($page->featured_image)
            <meta property="og:image" content="{{ $page->featured_image_url }}">
        @endif
    </x-slot>

    {{-- Drobcekova navigacia --}}
    <nav aria-label="Breadcrumb" class="mx-auto mb-6 max-w-3xl">
        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-subtle">
            <li><a href="{{ route('home') }}" class="hover:text-brand-600">Home</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('pages.index') }}" class="hover:text-brand-600">Articles</a></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page" class="max-w-[16rem] truncate text-text">{{ $page->title }}</li>
        </ol>
    </nav>

    <article class="mx-auto max-w-3xl">

        {{-- Hlavicka --}}
        <header class="mb-8">
            <span class="mb-4 block h-[3px] w-12 bg-brand-600" aria-hidden="true"></span>

            @if($page->is_featured)
                <x-ui.badge variant="dark" class="mb-3">Recommended article</x-ui.badge>
            @endif

            <h1 class="text-3xl sm:text-4xl md:text-5xl">{{ $page->title }}</h1>

            <ul class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-subtle">
                @if($page->author)
                    <li class="flex items-center gap-1.5">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $page->author->name }}
                    </li>
                @endif

                @if($page->published_at)
                    <li class="flex items-center gap-1.5">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <time class="num" datetime="{{ $page->published_at->toDateString() }}">{{ $page->published_at->format('d. F Y') }}</time>
                    </li>
                @endif

                <li class="flex items-center gap-1.5">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="square" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span><span class="num">{{ number_format($page->views_count) }}</span> Views</span>
                </li>
            </ul>
        </header>

        {{-- Titulny obrazok --}}
        @if($page->featured_image)
            <figure class="mb-8 overflow-hidden rounded-xs border border-line bg-tile">
                <img src="{{ $page->featured_image_url }}" alt="{{ $page->title }}" class="h-auto w-full">
            </figure>
        @endif

        {{-- Perex --}}
        @if($page->excerpt)
            <p class="mb-8 border-l-3 border-brand-600 pl-5 text-lg leading-relaxed text-muted md:text-xl">{{ $page->excerpt }}</p>
        @endif

        {{-- Obsah (HTML z administracie, sanitizovane v modeli) --}}
        <div class="article-content">
            {!! $page->safe_content !!}
        </div>

        {{-- Zdielanie --}}
        <div class="mt-12 border-t border-line pt-8">
            <h2 class="mb-4 text-xl">Share article</h2>
            <div class="flex flex-wrap gap-3">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="{{ $shareButton }}">
                    <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Facebook
                    <span class="sr-only">(opens in a new tab)</span>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($page->title) }}" target="_blank" rel="noopener noreferrer" class="{{ $shareButton }}">
                    <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                    Twitter
                    <span class="sr-only">(opens in a new tab)</span>
                </a>
                <button type="button" data-copy-url="{{ url()->current() }}" class="{{ $shareButton }}">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="square" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copy link
                </button>
            </div>
        </div>
    </article>

    {{-- Suvisiace clanky (sirsi kontajner nez text clanku) --}}
    @if($relatedPages->count() > 0)
        <section class="mt-16" aria-labelledby="related-articles">
            <x-ui.section-header heading-id="related-articles" eyebrow="Read next" title="Related articles" :href="route('pages.index')" linkText="All articles" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 lg:grid-cols-3">
                @foreach($relatedPages as $related)
                    <x-ui.article-card :page="$related" :heading-level="3" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.default-layout>
