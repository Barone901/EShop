{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
{{-- Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene. --}}
<section class="grid gap-6 lg:grid-cols-3 lg:gap-10">
    <header>
        <h3 class="text-xl text-danger-700">{{ __('Delete Account') }}</h3>
        <p class="mt-1 text-sm text-muted">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
    </header>

    <div class="lg:col-span-2 lg:self-center">
        <x-button variant="danger"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >{{ __('Delete Account') }}</x-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="border-t-3 border-danger-700 p-6" data-validate>
            @csrf
            @method('delete')

            <h2 class="text-2xl">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-2 text-sm text-muted">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1"
                    placeholder="{{ __('Password') }}"
                    required
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <x-button variant="secondary" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-button>

                <x-button variant="danger">
                    {{ __('Delete Account') }}
                </x-button>
            </div>
        </form>
    </x-modal>
</section>
