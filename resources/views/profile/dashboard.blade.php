<x-layouts.default-layout title="My Profile" type="app">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My profile
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 text-gray-900">
            <h3 class="text-lg font-semibold mb-2">Welcome, {{ $user->full_name }}!</h3>
            <p class="text-gray-600">Here you will find your account settings.</p>
        </div>
    </div>

    {{-- =============== PROFIL (Breeze partials) =============== --}}
    <div id="profile">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Profile settings</h3>

        <div class="space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-layouts.default-layout>
