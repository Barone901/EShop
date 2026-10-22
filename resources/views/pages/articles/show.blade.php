@php
    $title = $page->meta_title ?: $page->title;
    $description = $page->meta_description ?: $page->excerpt;
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

    <article class="max-w-4xl mx-auto">

        {{-- Header --}}
        <header class="mb-8">
            @if($page->is_featured)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 mb-4">
                    Recommended article
                </span>
            @endif

            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-900 mb-4">{{ $page->title }}</h1>

            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                @if($page->author)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-1" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ $page->author->name }}
                    </div>
                @endif

                @if($page->published_at)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-1" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <time datetime="{{ $page->published_at->toDateString() }}">
                            {{ $page->published_at->format('d. F Y') }}
                        </time>
                    </div>
                @endif

                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-1" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    {{ number_format($page->views_count) }} Views
                </div>
            </div>
        </header>

        {{-- Featured Image --}}
        @if($page->featured_image)
            <figure class="mb-8">
                <img
                    src="{{ $page->featured_image_url }}"
                    alt="{{ $page->title }}"
                    class="w-full h-auto rounded-lg shadow-lg"
                >
            </figure>
        @endif

        {{-- Excerpt --}}
        @if($page->excerpt)
            <div class="card mb-8 p-6 bg-indigo-50 rounded-lg border-l-4 border-indigo-500">
                <p class="text-lg text-indigo-200 italic">{{ $page->excerpt }}</p>
            </div>
        @endif

        {{-- Content (HTML z administrácie, sanitizované v modeli) --}}
        <div class="article-content">
            {!! $page->safe_content !!}
        </div>

        {{-- Share --}}
        <div class="mt-12 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Share article</h3>
            <div class="flex gap-3">
                <a
                    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Facebook
                </a>
                <a
                    href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($page->title) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors"
                >
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                    Twitter
                </a>
                <button
                    type="button"
                    data-copy-url="{{ url()->current() }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                >
                    <svg class="w-5 h-5 mr-2" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Copy link
                </button>
            </div>
        </div>

        {{-- Related Pages --}}
        @if($relatedPages->count() > 0)
            <section class="mt-12 pt-8 border-t border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Related articles</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedPages as $related)
                        <article class="article-card card bg-white rounded-lg shadow-md overflow-hidden">
                            @if($related->featured_image)
                                <a href="{{ route('pages.show', $related->slug) }}">
                                    <img
                                        src="{{ $related->featured_image_url }}"
                                        alt="{{ $related->title }}"
                                        class="w-full h-32 object-cover"
                                    >
                                </a>
                            @endif
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('pages.show', $related->slug) }}" class="hover:text-indigo-400">
                                        {{ $related->title }}
                                    </a>
                                </h4>
                                @if($related->excerpt)
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ $related->excerpt }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </article>
</x-layouts.default-layout>

