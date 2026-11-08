{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{-- Paticka (tmava zona). --}}
<footer class="site-footer theme-dark mt-auto text-steel-300">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 md:py-16 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Znacka --}}
            <div>
                <a href="{{ route('home') }}" class="inline-flex py-1.5" aria-label="{{ config('app.name', 'Eshop') }} – Home">
                    <img src="{{ asset('images/triumfLogo.png') }}" alt="Triumf" class="h-8 w-auto" width="705" height="158" loading="lazy"/>
                </a>
                <p class="mt-4 max-w-xs text-sm">{{ __('Professional tools for tradespeople and businesses.') }}</p>
            </div>

            {{-- Obchod --}}
            <nav aria-labelledby="footer-shop">
                <h2 id="footer-shop" class="text-base text-steel-100">{{ __('Shop') }}</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
                    <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="{{ route('pages.index') }}">{{ __('Articles') }}</a></li>
                    <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="{{ route('about') }}">{{ __('About us') }}</a></li>
                    <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="{{ route('contact') }}">{{ __('Contact') }}</a></li>
                </ul>
            </nav>

            {{-- Ucet --}}
            <nav aria-labelledby="footer-account">
                <h2 id="footer-account" class="text-base text-steel-100">{{ __('Account') }}</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    @auth
                        <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="{{ route('dashboard') }}">{{ __('My profile') }}</a></li>
                        <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="{{ route('orders.index') }}">{{ __('Orders') }}</a></li>
                        <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="{{ route('addresses.index') }}">{{ __('Addresses') }}</a></li>
                    @else
                        <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="{{ route('login') }}">{{ __('Log in') }}</a></li>
                        <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                    @endauth
                    <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="{{ route('cart.index') }}">{{ __('Cart') }}</a></li>
                </ul>
            </nav>

            {{-- Kontakt --}}
            <div>
                <h2 class="text-base text-steel-100">{{ __('Contact') }}</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="mailto:info@triumf.sk">info@triumf.sk</a></li>
                    <li><a class="inline-flex min-h-9 items-center hover:text-brand-400" href="tel:+421912345678">+421 912 345 678</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-ink-700">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-2 px-4 py-5 text-xs sm:px-6 lg:px-8">
            <p>{{ __('© :year :app. All rights reserved.', ['year' => now()->year, 'app' => config('app.name', 'Eshop')]) }}</p>
            <p class="eyebrow text-steel-300">{{ __('Professional tools') }}</p>
        </div>
    </div>
</footer>
