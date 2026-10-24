@php
    // Hlavne menu (desktop aj mobil): route => text odkazu
    $navLinks = [
        'home' => 'Home',
        'products.index' => 'Products',
        'pages.index' => 'Articles',
        'about' => 'About us',
        'contact' => 'Contact',
    ];
@endphp

<header class="site-header sticky top-0 z-50">
    {{-- ROW 1: logo + search + profile (account) --}}
    <div class="header-top bg-white border-b">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center gap-4">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/triumfLogo.png') }}" alt="Triumf" class="h-10 w-auto"/>
                </a>

                {{-- Right side --}}
                <div class="ml-auto flex items-center gap-3 w-full max-w-4xl justify-end">
                    {{-- Search (desktop) --}}
                    <div class="relative w-full max-w-xl hidden md:block">
                        <input
                            type="text"
                            id="search-input"
                            placeholder="Search product..."
                            aria-label="Search product"
                            autocomplete="off"
                            class="w-full rounded-md border border-gray-200 bg-gray-50 px-4 py-2 text-sm"
                        />
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                             stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>

                        <div id="search-results"
                             class="absolute top-full left-0 right-0 mt-1 bg-white border rounded-xl shadow-lg overflow-hidden z-50 hidden"></div>
                    </div>

                    {{-- Profile / account (desktop only, one place) --}}
                    <div class="hidden md:flex items-center">
                        @include('layouts.partials.account')
                    </div>
                </div>
            </div>

            {{-- Mobile search under row 1 --}}
            <div class="md:hidden pb-3 relative">
                <input
                    type="text"
                    id="search-input-mobile"
                    placeholder="Search product..."
                    aria-label="Search product"
                    autocomplete="off"
                    class="w-full rounded-md border border-gray-200 bg-gray-50 px-4 py-2 text-sm"
                />

                <div id="search-results-mobile"
                     class="absolute top-full left-0 right-0 -mt-2 bg-white border rounded-xl shadow-lg overflow-hidden z-50 hidden"></div>
            </div>
        </div>

        {{-- ROW 2: menu links + cart + mobile profile --}}
        <div class="header-bottom text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="h-12 flex items-center justify-between">
                    {{-- Desktop nav --}}
                    <nav class="site-nav hidden lg:flex items-center gap-8 text-sm font-medium">
                        @foreach($navLinks as $route => $label)
                            <a href="{{ route($route) }}" class="hover:opacity-90">{{ $label }}</a>
                        @endforeach
                    </nav>

                    {{-- Left side: cart always visible + menu only mobile --}}
                    <div class="flex items-center gap-2">
                        {{-- Cart (desktop + mobile) --}}
                        <div id="cart-wrapper" class="relative static sm:relative z-[60] md:z-10"
                             data-cart-url="{{ route('cart.index') }}"
                             data-checkout-url="{{ route('checkout') }}"
                             data-home-url="{{ route('home') }}">
                            <a
                                href="{{ route('cart.index') }}"
                                id="cart-btn"
                                class="relative inline-flex items-center gap-2 rounded-md border border-white/20 px-3 py-2 text-sm hover:bg-white/10"
                                aria-expanded="false"
                                aria-controls="cart-dropdown"
                            >
                                <svg class="w-5 h-5" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>

                                <span class="hidden sm:inline">Cart</span>

                                <span id="cart-count"
                                      class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center hidden">0</span>
                            </a>

                            <div
                                id="cart-dropdown"
                                class="hidden opacity-0 translate-y-2 transition-all duration-200 absolute sm:absolute top-full mt-2 z-50 overflow-hidden bg-[#141414] text-white border border-white/10 shadow-xl rounded-xl right-2 left-2 w-auto max-w-none sm:right-0 sm:left-auto sm:w-80"
                            >
                                <div class="p-3 border-b border-white/10">
                                    <h3 class="font-semibold text-sm">Cart</h3>
                                </div>

                                <div id="cart-dropdown-content">
                                    <div class="p-6 text-center text-white/60">
                                        <div class="animate-pulse">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Mobile menu button --}}
                        <button
                            id="mobile-menu-btn"
                            type="button"
                            class="lg:hidden inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-white/10"
                            aria-expanded="false"
                            aria-controls="mobile-nav"
                        >
                            <svg class="w-5 h-5" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            Menu
                        </button>
                    </div>

                    {{-- Right side: mobile profile only --}}
                    <div class="md:hidden flex items-center ml-auto">
                        <div id="mobile-profile-wrapper" class="relative static sm:relative z-[60]">
                            <button
                                id="mobile-profile-btn"
                                type="button"
                                aria-expanded="false"
                                aria-controls="mobile-profile-dropdown"
                                class="inline-flex items-center gap-2 rounded-md border border-white/20 px-3 py-2 text-sm hover:bg-white/10"
                            >
                                <svg class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 14a4 4 0 10-8 0m8 0a4 4 0 014 4v1H4v-1a4 4 0 014-4m8 0H8m4-6a4 4 0 110-8 4 4 0 010 8z"/>
                                </svg>

                                <span class="inline">My profile</span>

                                <svg id="mobile-profile-arrow" class="w-4 h-4 transition-transform duration-200" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div
                                id="mobile-profile-dropdown"
                                class="hidden opacity-0 translate-y-2 transition-all duration-200 absolute sm:absolute top-full mt-2 z-50 overflow-hidden
                                       bg-[#141414] text-white border border-white/10 shadow-xl rounded-xl
                                       right-2 left-auto w-[220px] max-w-[calc(100vw-1rem)]
                                       sm:right-0 sm:w-72"
                            >
                                @auth
                                    <div class="p-2">
                                        <div class="px-3 py-2 text-xs text-white/60 border-b border-white/10 mb-1">
                                            {{ Auth::user()->name }}
                                        </div>

                                        <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white/10">
                                            My profile
                                        </a>

                                        <a href="{{ route('orders.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white/10">
                                            Orders
                                        </a>

                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left rounded-md px-3 py-2 text-sm hover:bg-white/10">
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="p-2">
                                        <a href="{{ route('login') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white/10">
                                            Login
                                        </a>
                                        <a href="{{ route('register') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white/10">
                                            Register
                                        </a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile nav dropdown --}}
                <nav id="mobile-nav" class="lg:hidden hidden pb-3 border-t border-white/20 text-sm">
                    @foreach($navLinks as $route => $label)
                        <a href="{{ route($route) }}" class="block rounded-md px-3 py-2 hover:bg-white/10">{{ $label }}</a>
                    @endforeach
                </nav>
            </div>
        </div>
    </div>
</header>
