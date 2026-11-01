{{-- Zalozene na Laravel Breeze (https://github.com/laravel/breeze). --}}
<x-layouts.default-layout title="Registration" type="guest">
    <div class="mb-6">
        <p class="eyebrow">Customer account</p>
        <h1 class="mt-2 text-3xl">Registration</h1>
        <p class="mt-1 text-sm text-muted">Create your profile to save addresses and track your orders.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" data-validate class="space-y-5">
        @csrf

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Name')" class="mb-1.5" />
            <x-text-input
                id="name"
                type="text"
                name="name"
                :value="old('name')"
                required
                minlength="2"
                maxlength="255"
                :pattern="$patterns['name']"
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        {{-- Email Address --}}
        <div>
            <x-input-label for="email" :value="__('Email')" class="mb-1.5" />
            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                maxlength="255"
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
                required
                data-rules="password"
                autocomplete="new-password"
                aria-describedby="password-hint"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            <p id="password-hint" class="mt-1.5 text-xs text-subtle">
                The password must be at least 8 characters long and contain upper and lower case letters and numbers.
            </p>
        </div>

        {{-- Confirm Password --}}
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm password')" class="mb-1.5" />
            <x-text-input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                data-rules="match:#password:The passwords do not match."
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <x-button variant="primary" size="lg" class="w-full">
            Register
        </x-button>
    </form>

    <div class="mt-8 border-t border-line pt-6 text-center">
        <p class="text-sm text-muted">Already have an account?</p>
        <x-button :href="route('login')" variant="secondary" class="mt-3 w-full">
            Log in
        </x-button>
    </div>
</x-layouts.default-layout>
