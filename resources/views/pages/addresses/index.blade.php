<x-layouts.default-layout title="My Addresses">

    <div>

        {{-- Page Header --}}
        <x-ui.page-header title="My Addresses" subtitle="Manage your shipping and billing addresses." />

        {{-- Flash Messages --}}
        <x-ui.flash-messages />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Shipping Addresses --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <svg class="inline-block w-6 h-6 mr-2 text-indigo-200" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Shipping Addresses
                    </h2>
                    <a href="{{ route('addresses.create', ['type' => 'shipping']) }}"
                       class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-1" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add
                    </a>
                </div>

                @if($shippingAddresses->isEmpty())
                    <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                        <p class="text-gray-500 mb-4">You don't have any shipping addresses yet</p>
                        <a href="{{ route('addresses.create', ['type' => 'shipping']) }}" class="text-indigo-400 hover:text-indigo-300 font-medium">
                            Add shipping address →
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($shippingAddresses as $address)
                            <x-shop.address-card :address="$address" />
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Billing Addresses --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <svg class="inline-block w-6 h-6 mr-2 text-green-200" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Billing Addresses
                    </h2>
                    <a href="{{ route('addresses.create', ['type' => 'billing']) }}"
                       class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-1" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add
                    </a>
                </div>

                @if($billingAddresses->isEmpty())
                    <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 mb-4">You don't have any billing addresses yet</p>
                        <a href="{{ route('addresses.create', ['type' => 'billing']) }}" class="text-green-400 hover:text-green-300 font-medium">
                            Add billing address →
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($billingAddresses as $address)
                            <x-shop.address-card :address="$address" />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Back to Dashboard --}}
        <div class="mt-8 text-center">
            <a href="{{ route('dashboard') }}" class="font-medium text-white underline-offset-4 hover:underline">
                ← Back to Dashboard
            </a>
        </div>
    </div>

</x-layouts.default-layout>

