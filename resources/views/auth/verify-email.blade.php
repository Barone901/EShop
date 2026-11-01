{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
{{-- Zalozene na Laravel Breeze (https://github.com/laravel/breeze). --}}
<x-layouts.default-layout title="Verify Email" type="guest">
    <div class="mb-6">
        <span class="mb-4 grid size-12 place-items-center rounded-xs bg-brand-50 text-brand-600" aria-hidden="true">
            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="square" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </span>
        <p class="eyebrow">Customer account</p>
        <h1 class="mt-2 text-3xl">Verify your email</h1>
    </div>

    <p class="mb-6 text-sm leading-relaxed text-muted">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mb-6" :status="__('A new verification link has been sent to the email address you provided during registration.')" />
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-button variant="primary" class="w-full sm:w-auto">
                {{ __('Resend Verification Email') }}
            </x-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <x-button type="submit" variant="ghost" class="w-full sm:w-auto">
                {{ __('Log Out') }}
            </x-button>
        </form>
    </div>
</x-layouts.default-layout>
