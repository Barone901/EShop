{{-- [AI] Vygenerovane s pomocou AI (Claude) podla vychodzieho Laravel view (vendor/laravel/framework/.../pagination/tailwind.blade.php). --}}
{{--
    Strankovanie v dizajne obchodu (pouzitie: $paginator->links('pagination.shop')).
    Logika je rovnaka ako vo vychodzom Laravel tailwind view, zmeneny je len vzhlad.
--}}
@php
    // Spolocny tvar bunky (bez farieb, aby sa varianty nebili)
    $cell = 'inline-flex min-h-11 min-w-11 items-center justify-center border px-3 text-sm';
    $link = $cell . ' border-line bg-white text-text transition-colors hover:border-brand-600 hover:text-brand-600';
    $disabled = $cell . ' cursor-not-allowed border-line bg-white text-line-strong';
    $current = $cell . ' num border-brand-600 bg-brand-600 text-white';
    $dots = $cell . ' border-transparent text-subtle';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between gap-4">
        {{-- Predosla --}}
        @if ($paginator->onFirstPage())
            <span class="{{ $disabled }} gap-1" aria-disabled="true">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span class="font-display font-semibold uppercase tracking-wider">{{ __('Previous') }}</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $link }} gap-1">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span class="font-display font-semibold uppercase tracking-wider">{{ __('Previous') }}</span>
            </a>
        @endif

        {{-- Mobil: len "strana x / y" --}}
        <p class="num text-sm text-muted sm:hidden">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</p>

        {{-- Cisla stran (od sm) --}}
        <ul class="hidden items-center gap-1 sm:flex">
            @foreach ($elements as $element)
                {{-- Oddelovac "..." --}}
                @if (is_string($element))
                    <li><span class="{{ $dots }}" aria-disabled="true">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="{{ $current }}">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $link }} num" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>

        {{-- Dalsia --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $link }} gap-1">
                <span class="font-display font-semibold uppercase tracking-wider">{{ __('Next') }}</span>
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="{{ $disabled }} gap-1" aria-disabled="true">
                <span class="font-display font-semibold uppercase tracking-wider">{{ __('Next') }}</span>
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif
    </nav>
@endif
