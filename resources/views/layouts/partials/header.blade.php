{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{--
    Hlavicka (tmava zona). JS vazby - nemen ID, aria-* ani pociatocne triedy dropdownov:
    - menu.js: #mobile-menu-btn/#mobile-nav, #mobile-profile-btn/-dropdown/-arrow, #cart-wrapper/#cart-btn/#cart-dropdown
      (dropdowny zacinaju zatvorene: "hidden opacity-0 translate-y-2", stav v aria-expanded)
    - cart.js: #cart-count (trieda hidden), #cart-dropdown-content (obsah = HTML zo servera, x-shop.cart-dropdown)
    - search.js: #search-input/#search-results, #search-input-mobile/#search-results-mobile
    Desktop ucet (#account-*) je v partials/account.
--}}
@php
    // Hlavne menu (desktop aj mobil): route => [text odkazu, vzor rout pre aktivny stav]
    $navLinks = [
        'home' => [__('Home'), 'home'],
        'products.index' => [__('Products'), ['products.*', 'product.*']],
        'pages.index' => [__('Articles'), 'pages.*'],
        'about' => [__('About us'), 'about'],
        'contact' => [__('Contact'), 'contact'],
    ];

    // Spolocne triedy dropdown panelov (svetly panel pod tmavou hlavickou)
    $panelClasses = 'theme-light hidden opacity-0 translate-y-2 transition-all duration-200 absolute top-full mt-2 z-50 overflow-hidden rounded-xs border border-line bg-white text-text shadow-2xl';

    // Pole vyhladavania v tmavej hlavicke
    $searchClasses = 'block w-full min-h-11 border-ink-700 bg-ink-800 py-2 pl-10 pr-3 text-sm text-steel-100 placeholder:text-steel-300 focus:border-brand-500 focus:outline-brand-500';
@endphp

<header class="site-header theme-dark sticky top-0 z-50 border-b-3 border-brand-500 bg-ink-900 text-steel-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- RIADOK 1: menu (mobil) + logo + vyhladavanie + ucet + kosik --}}
        <div class="flex h-16 items-center gap-2 sm:gap-4 md:h-20 md:gap-6">
            {{-- Tlacidlo mobilneho menu --}}
            <button
                id="mobile-menu-btn"
                type="button"
                class="-ml-2 inline-flex min-h-11 min-w-11 items-center justify-center gap-2 rounded-xs px-2 text-steel-100 hover:bg-ink-800 lg:hidden"
                aria-expanded="false"
                aria-controls="mobile-nav"
            >
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="square" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="sr-only sm:not-sr-only sm:font-display sm:text-base sm:font-semibold sm:uppercase sm:tracking-wider">{{ __('Menu') }}</span>
            </button>

            {{-- Logo (cervene na priehladnom pozadi) priamo na tmavej hlavicke --}}
            <a href="{{ route('home') }}" class="flex shrink-0 items-center py-1.5" aria-label="{{ config('app.name', 'Eshop') }} – Home">
                <img src="{{ asset('images/triumfLogo.png') }}" alt="Triumf" class="h-7 w-auto md:h-9" width="705" height="158"/>
            </a>

            {{-- Vyhladavanie (desktop) --}}
            <div class="relative hidden max-w-xl flex-1 md:block" role="search">
                <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-steel-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="square" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    id="search-input"
                    placeholder="{{ __('Search products...') }}"
                    aria-label="{{ __('Search products') }}"
                    autocomplete="off"
                    class="{{ $searchClasses }}"
                />

                <div id="search-results"
                     class="theme-light absolute left-0 right-0 top-full z-50 mt-2 hidden max-h-[70vh] overflow-y-auto rounded-xs border border-line bg-white text-text shadow-2xl"></div>
            </div>

            <div class="ml-auto flex items-center gap-1 sm:gap-2">
                {{-- Ucet (desktop): prihlaseny = dropdown, host = odkazy --}}
                <div class="hidden md:block">
                    @include('layouts.partials.account')
                </div>

                {{-- Ucet (mobil): dropdown pre prihlaseneho aj hosta --}}
                <div id="mobile-profile-wrapper" class="md:hidden">
                    <button
                        id="mobile-profile-btn"
                        type="button"
                        aria-expanded="false"
                        aria-controls="mobile-profile-dropdown"
                        aria-label="{{ __('My account') }}"
                        class="inline-flex min-h-11 items-center gap-1 rounded-xs px-2 text-steel-100 hover:bg-ink-800"
                    >
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="square" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <svg id="mobile-profile-arrow" class="size-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="square" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="mobile-profile-dropdown" class="{{ $panelClasses }} right-4 w-64 max-w-[calc(100vw-2rem)]">
                        @auth
                            <div class="border-b border-line px-4 py-3">
                                <p class="truncate text-sm font-semibold">{{ Auth::user()->name }}</p>
                                <p class="truncate text-xs text-subtle">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="flex min-h-11 items-center px-4 text-sm hover:bg-tile hover:text-brand-600">{{ __('My profile') }}</a>
                                <a href="{{ route('orders.index') }}" class="flex min-h-11 items-center px-4 text-sm hover:bg-tile hover:text-brand-600">{{ __('Orders') }}</a>
                                <a href="{{ route('addresses.index') }}" class="flex min-h-11 items-center px-4 text-sm hover:bg-tile hover:text-brand-600">{{ __('Addresses') }}</a>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-line py-1">
                                @csrf
                                <button type="submit" class="flex min-h-11 w-full items-center px-4 text-left text-sm hover:bg-tile hover:text-brand-600">
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        @else
                            <div class="space-y-2 p-3">
                                <x-button :href="route('login')" class="w-full">{{ __('Log in') }}</x-button>
                                <x-button :href="route('register')" variant="secondary" class="w-full">{{ __('Register') }}</x-button>
                            </div>
                        @endauth
                    </div>
                </div>

                {{-- Kosik: hover otvori nahlad (menu.js), klik vedie na stranku kosika --}}
                <div id="cart-wrapper" class="static sm:relative">
                    <a
                        href="{{ route('cart.index') }}"
                        id="cart-btn"
                        class="relative inline-flex min-h-11 items-center gap-2 rounded-xs border border-ink-700 px-3 font-display text-base font-semibold uppercase tracking-wider text-steel-100 transition-colors hover:border-brand-500"
                        aria-expanded="false"
                        aria-controls="cart-dropdown"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="square" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>

                        <span class="hidden sm:inline">{{ __('Cart') }}</span>

                        <span id="cart-count"
                              class="absolute -right-2 -top-2 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-600 px-1 font-sans text-xs font-bold text-white ring-2 ring-ink-900 hidden">0</span>
                    </a>

                    <div id="cart-dropdown" class="{{ $panelClasses }} left-4 right-4 sm:left-auto sm:right-0 sm:w-96">
                        <div class="flex items-center justify-between border-b border-line px-4 py-3">
                            <h3 class="text-lg">{{ __('Cart') }}</h3>
                        </div>

                        <div id="cart-dropdown-content">
                            <div class="p-6 text-center text-sm text-subtle">
                                <div class="animate-pulse">{{ __('Loading...') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Vyhladavanie (mobil) --}}
        <div class="relative pb-3 md:hidden" role="search">
            <svg class="pointer-events-none absolute left-3 top-[1.375rem] size-4 -translate-y-1/2 text-steel-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="square" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                id="search-input-mobile"
                placeholder="{{ __('Search products...') }}"
                aria-label="{{ __('Search products') }}"
                autocomplete="off"
                class="{{ $searchClasses }}"
            />

            <div id="search-results-mobile"
                 class="theme-light absolute left-0 right-0 top-full z-50 -mt-1 hidden max-h-[70vh] overflow-y-auto rounded-xs border border-line bg-white text-text shadow-2xl"></div>
        </div>
    </div>

    {{-- RIADOK 2 (desktop): navigacia + kontakt --}}
    <div class="hidden border-t border-ink-700 bg-ink-950 lg:block">
        <div class="mx-auto flex h-12 max-w-7xl items-stretch justify-between px-4 sm:px-6 lg:px-8">
            <nav class="flex items-stretch gap-8" aria-label="{{ __('Main') }}">
                @foreach($navLinks as $route => [$label, $pattern])
                    @php $active = request()->routeIs($pattern); @endphp
                    <a href="{{ route($route) }}"
                       @if($active) aria-current="page" @endif
                       @class([
                           'relative inline-flex items-center font-display text-base font-semibold uppercase tracking-wider transition-colors',
                           'text-steel-100 after:absolute after:inset-x-0 after:bottom-0 after:h-[3px] after:bg-brand-500' => $active,
                           'text-steel-300 hover:text-steel-100' => !$active,
                       ])>{{ $label }}</a>
                @endforeach
            </nav>

        </div>
    </div>

    {{-- Mobilna navigacia (menu.js prepina triedu hidden) --}}
    <nav id="mobile-nav" class="hidden border-t border-ink-700 bg-ink-900 lg:hidden" aria-label="{{ __('Main') }}">
        <div class="mx-auto max-w-7xl px-4 py-2 sm:px-6">
            @foreach($navLinks as $route => [$label, $pattern])
                @php $active = request()->routeIs($pattern); @endphp
                <a href="{{ route($route) }}"
                   @if($active) aria-current="page" @endif
                   @class([
                       'flex min-h-12 items-center border-l-3 pl-4 font-display text-lg font-semibold uppercase tracking-wider',
                       'border-brand-500 text-steel-100' => $active,
                       'border-transparent text-steel-300 hover:text-steel-100' => !$active,
                   ])>{{ $label }}</a>
            @endforeach
        </div>
    </nav>
</header>
