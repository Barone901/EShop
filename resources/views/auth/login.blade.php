{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{-- Zalozene na Laravel Breeze (https://github.com/laravel/breeze). Reset hesla neexistuje - nepridavaj odkaz "Forgot password". --}}
<x-layouts.default-layout title="Log in" type="guest">
    <div class="mb-6">
        <p class="eyebrow">Customer account</p>
        <h1 class="mt-2 text-3xl">Log in</h1>
        <p class="mt-1 text-sm text-muted">Welcome back! Log in to see your orders and saved addresses.</p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" data-validate class="space-y-5">
        @csrf

        {{-- Email Address --}}
        <div>
            <x-input-label for="email" :value="__('Email')" class="mb-1.5" />
            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                data-rules="required|email"
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- Password --}}
        <div>
            <x-input-label for="password" :value="__('Password')" class="mb-1.5" />
            <x-text-input
                id="password"
                type="password"
                name="password"
                data-rules="required|minLength:8"
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        {{-- Remember Me --}}
        <label for="remember_me" class="inline-flex min-h-11 cursor-pointer items-center gap-2.5">
            <input id="remember_me" type="checkbox" class="size-5" name="remember">
            <span class="text-sm">Remember me</span>
        </label>

        <x-button variant="primary" size="lg" class="w-full">
            Log in
        </x-button>
    </form>

    <div class="mt-8 border-t border-line pt-6 text-center">
        <p class="text-sm text-muted">Don't have an account?</p>
        <x-button :href="route('register')" variant="secondary" class="mt-3 w-full">
            Sign up
        </x-button>
    </div>
</x-layouts.default-layout>
