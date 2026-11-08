{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Mriezka kategorii (homepage). Karta = odkaz na vypis produktov s filtrom kategorie.
    Vsetky fotky: rovnaky pomer 4:3, podklad tile, object-contain (nic sa neoreze).
--}}
@props(['categories'])

<ul class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:gap-6 lg:grid-cols-4 xl:grid-cols-5">
    @foreach($categories as $category)
        <li>
            <a
                href="{{ route('products.index', ['category' => $category->slug]) }}"
                class="card group flex h-full flex-col transition-colors hover:border-brand-600"
            >
                <div class="aspect-[4/3] overflow-hidden border-b border-line bg-tile">
                    @if($category->image_src)
                        <img
                            src="{{ $category->image_src }}"
                            alt=""
                            class="h-full w-full object-contain p-4 mix-blend-multiply transition-transform duration-300 group-hover:scale-[1.04] motion-reduce:transition-none"
                            loading="lazy"
                        >
                    @else
                        {{-- Placeholder rovnakej velkosti: iniciala kategorie v ramceku + ikona naradia --}}
                        <span class="flex h-full w-full flex-col items-center justify-center gap-2 p-4" aria-hidden="true">
                            <span class="grid size-20 place-items-center rounded-xs border-2 border-dashed border-line-strong font-display text-5xl font-bold uppercase text-line-strong">
                                {{ Str::substr($category->name, 0, 1) }}
                            </span>
                            <span class="eyebrow">{{ __('No image') }}</span>
                        </span>
                    @endif
                </div>

                <div class="flex flex-1 items-center justify-between gap-2 p-4">
                    <span class="font-display text-lg font-semibold uppercase leading-tight tracking-wide group-hover:text-brand-600">
                        {{ $category->name }}
                    </span>
                    <svg class="size-5 shrink-0 text-subtle transition-transform group-hover:translate-x-0.5 group-hover:text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="square" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        </li>
    @endforeach
</ul>
