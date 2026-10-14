<div class="relative" id="account-wrapper">
    <button
        type="button"
        id="account-btn"
        aria-expanded="false"
        aria-controls="account-dropdown"
        class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-white/90 hover:text-white hover:bg-white/10 transition"
    >
        <svg class="w-5 h-5" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>

        <span class="hidden sm:inline font-medium">
            @auth
                {{ Auth::user()->name }}
            @else
                My profile
            @endauth
        </span>

        <svg id="account-arrow"
             class="w-4 h-4 opacity-80 transition-transform"
             stroke="currentColor"
             viewBox="0 0 24 24"
             aria-hidden="true">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        id="account-dropdown"
        class="hidden opacity-0 translate-y-2 transition-all duration-200 absolute right-0 top-full mt-2 w-56 bg-[#141414] text-white rounded-xl border border-white/10 shadow-xl z-50 overflow-hidden"
    >
        @auth
            <div class="p-3 border-b border-white/10">
                <p class="font-semibold text-sm">{{ Auth::user()->name }}</p>
                <p class="text-xs text-white/60">{{ Auth::user()->email }}</p>
            </div>

            <div class="py-2">
                <a href="{{ route('dashboard') }}"
                   class="block px-4 py-2 text-sm text-white/90 hover:bg-white/10">My profile</a>
            </div>

            <div class="border-t border-white/10 py-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-white/90 hover:bg-white/10">
                        Logout
                    </button>
                </form>
            </div>
        @else
            <div class="p-3 border-b border-white/10">
                <p class="font-semibold text-sm">My profile</p>
                <p class="text-xs text-white/60">Log in or create profile</p>
            </div>

            <div class="py-2">
                <a href="{{ route('login') }}"
                   class="block px-4 py-2 text-sm text-white/90 hover:bg-white/10">Log in</a>
                <a href="{{ route('register') }}"
                   class="block px-4 py-2 text-sm text-white/90 hover:bg-white/10">Register</a>
            </div>
        @endauth
    </div>
</div>
