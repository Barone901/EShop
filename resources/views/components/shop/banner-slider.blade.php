@props(['banners' => null])

@if($banners && $banners->isNotEmpty())
    <section id="banner-slider" class="relative w-full bg-black overflow-hidden">
        {{-- Full-width pás, ale obsah banneru má limit (na 4K) --}}
        <div class="relative mx-auto w-full max-w-[1600px]">
            <div class="banner-slider-container relative">
                {{-- SLIDES AREA --}}
                <div class="relative h-[240px] sm:h-[340px] lg:h-[460px]">
                    <div class="banner-slides flex h-full transition-transform duration-500 ease-in-out">
                        @foreach($banners as $banner)
                            <div class="banner-slide min-w-full h-full">
                                @if(!empty($banner->link))
                                    <a href="{{ $banner->link }}" class="block h-full">
                                @endif

                                {{-- Tu je kľúč: object-contain + čierne pozadie => nič sa neoreže --}}
                                <div class="relative h-full w-full bg-black">
                                    <img
                                        src="{{ $banner->image_url }}"
                                        alt="{{ $banner->name }}"
                                        class="absolute inset-0 w-full h-full object-contain select-none"
                                        draggable="false"
                                    />
                                </div>

                                @if(!empty($banner->link))
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- CONTROLS BAR (space under image) --}}
                @if($banners->count() > 1)
                    <div class="relative z-20 bg-black">
                        <div class="mx-auto flex items-center justify-end gap-3 px-4 py-3">
                            <div class="flex items-center overflow-hidden rounded-xl border border-white/10 bg-white/90 backdrop-blur shadow-lg">
                                {{-- Dots --}}
                                <div class="banner-dots flex items-center gap-2 px-4 py-2">
                                    @foreach($banners as $index => $banner)
                                        <button
                                            type="button"
                                            aria-label="Go to slide {{ $index + 1 }}"
                                            aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                            class="banner-dot"
                                        ></button>
                                    @endforeach
                                </div>

                                {{-- Divider --}}
                                <div class="h-10 w-px bg-black/10"></div>

                                {{-- Prev / Next --}}
                                <button
                                    type="button"
                                    aria-label="Previous slide"
                                    class="banner-prev grid h-10 w-12 place-items-center hover:bg-black/5 transition"
                                >
                                    <svg class="h-5 w-5 text-gray-800" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>

                                <div class="h-10 w-px bg-black/10"></div>

                                <button
                                    type="button"
                                    aria-label="Next slide"
                                    class="banner-next grid h-10 w-12 place-items-center hover:bg-black/5 transition"
                                >
                                    <svg class="h-5 w-5 text-gray-800" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
