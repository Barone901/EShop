@php
    $title = 'Articles and news';
@endphp

<x-layouts.default-layout :title="$title">

    <x-ui.page-header eyebrow="Knowledge" :title="$title" subtitle="Read our latest articles and news" />

    @if($pages->count() > 0)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 lg:grid-cols-3">
            @foreach($pages as $page)
                <x-ui.article-card :page="$page" />
            @endforeach
        </div>

        <div class="mt-10">
            {{ $pages->links('pagination.shop') }}
        </div>
    @else
        <x-ui.card padding="lg" class="text-center">
            <svg class="mx-auto mb-4 size-12 text-line-strong" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="square" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h2 class="text-2xl">No articles</h2>
            <p class="mt-2 text-muted">There are currently no articles available.</p>
        </x-ui.card>
    @endif
</x-layouts.default-layout>
