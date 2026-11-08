{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Banner slider (sucast hero na homepage, sirku urcuje rodic).
    JS (banner-slider.js) sa viaze na #banner-slider a data-banner-* atributy - nemen ich.
    Aktivnu bodku nastavuje JS cez aria-current, styl je v app.css.
    Pomer stran = pomer bannerov (671 x 375), preto object-cover nic neoreze a nevzniknu tmave okraje.
--}}
@props(['banners' => null])

@if($banners && $banners->isNotEmpty())
    <section id="banner-slider" class="relative overflow-hidden rounded-xs bg-ink-950" aria-roledescription="carousel" aria-label="{{ __('Featured') }}">
        <div class="relative aspect-[671/375] w-full">
            <div data-banner-slides class="flex h-full transition-transform duration-500 ease-in-out motion-reduce:transition-none">
                @foreach($banners as $banner)
                    <div data-banner-slide class="relative h-full min-w-full" aria-roledescription="slide" aria-label="{{ $loop->iteration }} / {{ $loop->count }}">
                        @if(!empty($banner->link))
                            <a href="{{ $banner->link }}" class="absolute inset-0 block">
                        @endif

                        <img
                            src="{{ $banner->image_url }}"
                            alt="{{ $banner->name }}"
                            class="absolute inset-0 h-full w-full object-cover select-none"
                            draggable="false"
                            @if(!$loop->first) loading="lazy" @endif
                        />

                        @if(!empty($banner->link))
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Ovladanie vnutri slidera (pravy dolny roh, mimo textu bannerov vlavo) --}}
            @if($banners->count() > 1)
                <div class="absolute bottom-2 right-2 z-10 flex items-stretch rounded-xs bg-ink-950/80 sm:bottom-3 sm:right-3">
                    <div class="flex items-center gap-2 px-3">
                        @foreach($banners as $index => $banner)
                            <button
                                type="button"
                                data-banner-dot
                                aria-label="{{ __('Go to slide :value', ['value' => $index + 1]) }}"
                                aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                            ></button>
                        @endforeach
                    </div>

                    <button
                        type="button"
                        data-banner-prev
                        aria-label="{{ __('Previous slide') }}"
                        class="grid size-10 place-items-center border-l border-white/15 text-steel-100 transition-colors hover:bg-ink-800 sm:size-11"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="square" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <button
                        type="button"
                        data-banner-next
                        aria-label="{{ __('Next slide') }}"
                        class="grid size-10 place-items-center border-l border-white/15 text-steel-100 transition-colors hover:bg-ink-800 sm:size-11"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="square" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    </section>
@endif
