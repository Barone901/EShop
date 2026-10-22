@php
    $title = 'Articles and news';
@endphp

<x-layouts.default-layout :title="$title">

    <x-ui.page-header :title="$title" subtitle="Read our latest articles and news" />

    @if($pages->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pages as $page)
                <x-ui.article-card :page="$page" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $pages->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No articles</h3>
            <p class="mt-1 text-sm text-gray-500">There are currently no articles available.</p>
        </div>
    @endif
</x-layouts.default-layout>

