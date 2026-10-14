@php
    // Hlavne menu (desktop aj mobil): route => text odkazu
    $navLinks = [
        'home' => 'Home',
        'products.index' => 'Products',
    ];
@endphp

<header class="site-header sticky top-0 z-50">
    {{-- ROW 1: logo + profile (account) --}}
    <div class="header-top bg-white border-b">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center gap-4">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/triumfLogo.png') }}" alt="Triumf" class="h-10 w-auto"/>
                </a>

                {{-- Right side --}}
                <div class="ml-auto flex items-center gap-3 justify-end">
                    {{-- Profile / account (desktop only, one place) --}}
                    <div class="hidden md:flex items-center">
                        @include('layouts.partials.account')
                    </div>
                </div>
            </div>

        </div>

        {{-- ROW 2: menu links + mobile profile --}}
        <div class="header-bottom text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="h-12 flex items-center justify-between">
                    {{-- Desktop nav --}}
                    <nav class="site-nav hidden lg:flex items-center gap-8 text-sm font-medium">
                        @foreach($navLinks as $route => $label)
                            <a href="{{ route($route) }}" class="hover:opacity-90">{{ $label }}</a>
                        @endforeach
                    </nav>

                    {{-- Left side: menu only mobile --}}
                    <div class="flex items-center gap-2">
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
