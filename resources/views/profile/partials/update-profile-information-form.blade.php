{{-- [AI] Upravene s pomocou AI (Claude). --}}
{{-- Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene. --}}
<section class="grid gap-6 lg:grid-cols-3 lg:gap-10">
    <header>
        <h3 class="text-xl">{{ __('Profile Information') }}</h3>
        <p class="mt-1 text-sm text-muted">{{ __("Update your account's profile information and email address.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="max-w-xl space-y-5 lg:col-span-2" data-validate>
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" maxlength="100" :pattern="$profilePatterns['first_name']" autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <div>
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" maxlength="100" :pattern="$profilePatterns['last_name']" autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Username')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required minlength="2" maxlength="255" :pattern="$profilePatterns['name']" autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required maxlength="255" autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 border-l-3 border-warn-700 bg-warn-50 px-3 py-2 text-sm text-warn-700">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="font-semibold underline underline-offset-2 hover:no-underline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-ok-700" role="status">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->phone)" placeholder="+1 XXX XXX XXXX" maxlength="20" :pattern="$profilePatterns['phone']" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div class="flex items-center gap-4">
            <x-button variant="primary">{{ __('Save') }}</x-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-ok-700"
                    role="status"
                >✓ {{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
