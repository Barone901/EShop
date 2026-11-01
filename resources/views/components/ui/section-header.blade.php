{{-- [AI] Vygenerovane s pomocou AI (Claude) pri redizajne. --}}
{{--
    Hlavicka sekcie na stranke (h2): akcentovy pruh, volitelny technicky popis (eyebrow)
    a volitelny odkaz vpravo (napr. "View all"). headingId = id nadpisu pre aria-labelledby sekcie.
--}}
@props(['title', 'eyebrow' => null, 'href' => null, 'linkText' => null, 'headingId' => null])

<div {{ $attributes->class(['mb-6 flex flex-wrap items-end justify-between gap-4 border-b border-line pb-4']) }}>
    <div class="border-l-3 border-brand-600 pl-4">
        @if($eyebrow)
            <p class="eyebrow mb-1">{{ $eyebrow }}</p>
        @endif
        <h2 @if($headingId) id="{{ $headingId }}" @endif class="text-2xl md:text-3xl">{{ $title }}</h2>
    </div>

    @if($href && $linkText)
        <a href="{{ $href }}" class="group inline-flex min-h-11 items-center gap-2 font-display text-base font-semibold uppercase tracking-wider text-brand-600 hover:text-brand-700">
            {{ $linkText }}
            <svg class="size-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="square" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    @endif
</div>
