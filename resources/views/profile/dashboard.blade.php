<x-layouts.default-layout title="My Profile" type="app">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My profile
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h3 class="text-lg font-semibold mb-2">Welcome, {{ $user->full_name }}!</h3>
            <p class="text-gray-600">Here you will find your account settings.</p>
        </div>
    </div>
</x-layouts.default-layout>
