{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
{{--
    Ucet v hlavicke (desktop).
    - prihlaseny: dropdown (menu.js: #account-btn, #account-dropdown, #account-arrow - nemen ID ani
      pociatocne triedy "hidden opacity-0 translate-y-2")
    - host: priame odkazy Log in / Register (bez dropdownu; menu.js chybajuce prvky preskoci)
--}}
@auth
    <div class="relative" id="account-wrapper">
        <button
            type="button"
            id="account-btn"
            aria-expanded="false"
            aria-controls="account-dropdown"
            class="inline-flex min-h-11 items-center gap-2.5 rounded-xs px-2 text-sm text-steel-100 transition-colors hover:bg-ink-800"
        >
            <span class="grid size-8 place-items-center rounded-xs bg-ink-700 font-display text-base font-bold uppercase" aria-hidden="true">
                {{ mb_substr(Auth::user()->name, 0, 1) }}
            </span>

            <span class="max-w-40 truncate font-medium">{{ Auth::user()->name }}</span>

            <svg id="account-arrow" class="size-4 text-steel-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="square" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div
            id="account-dropdown"
            class="theme-light hidden opacity-0 translate-y-2 transition-all duration-200 absolute right-0 top-full z-50 mt-2 w-64 overflow-hidden rounded-xs border border-line bg-white text-text shadow-2xl"
        >
            <div class="border-b border-line px-4 py-3">
                <p class="truncate text-sm font-semibold">{{ Auth::user()->name }}</p>
                <p class="truncate text-xs text-subtle">{{ Auth::user()->email }}</p>
            </div>

            <div class="py-1">
                <a href="{{ route('dashboard') }}" class="flex min-h-11 items-center px-4 text-sm hover:bg-tile hover:text-brand-600">My profile</a>
                <a href="{{ route('orders.index') }}" class="flex min-h-11 items-center px-4 text-sm hover:bg-tile hover:text-brand-600">My Orders</a>
                <a href="{{ route('addresses.index') }}" class="flex min-h-11 items-center px-4 text-sm hover:bg-tile hover:text-brand-600">Addresses</a>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="border-t border-line py-1">
                @csrf
                <button type="submit" class="flex min-h-11 w-full items-center px-4 text-left text-sm hover:bg-tile hover:text-brand-600">
                    Logout
                </button>
            </form>
        </div>
    </div>
@else
    <div class="flex items-center gap-2">
        <a href="{{ route('login') }}"
           class="inline-flex min-h-11 items-center px-3 font-display text-base font-semibold uppercase tracking-wider text-steel-100 hover:text-brand-400">
            Log in
        </a>
        <x-button :href="route('register')" variant="dark">Register</x-button>
    </div>
@endauth
