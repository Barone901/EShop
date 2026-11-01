{{--
    Hlavicka stranky (h1). Volitelne: eyebrow (technicky popis nad nadpisom), slot $actions vpravo.
--}}
@props(['title', 'subtitle' => null, 'eyebrow' => null])

<div class="mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-line pb-6">
    <div>
        <span class="mb-4 block h-[3px] w-12 bg-brand-600" aria-hidden="true"></span>
        @if($eyebrow)
            <p class="eyebrow mb-2">{{ $eyebrow }}</p>
        @endif
        <h1 class="text-3xl md:text-4xl">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-2 text-muted">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-3">{{ $actions }}</div>
    @endisset
</div>
