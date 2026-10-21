<x-layouts.default-layout title="My Profile" type="app">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                My profile
            </h2>
        </div>
    </x-slot>

    <div>
        <div>
            {{-- Welcome Section --}}
            <div class="card bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Welcome, {{ $user->full_name }}!</h3>
                    <p class="text-gray-600">Here you will find an overview of your orders, addresses, and account settings.</p>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <a href="{{ route('orders.index') }}" class="card bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow p-6 flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">My orders</h4>
                        <p class="text-sm text-gray-500">History</p>
                    </div>
                </a>

                <a href="{{ route('addresses.index') }}" class="card bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow p-6 flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">My addresses</h4>
                        <p class="text-sm text-gray-500">Address management</p>
                    </div>
                </a>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 card bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Latest orders</h3>
                            <a href="{{ route('orders.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors">
                                Show all →
                            </a>
                        </div>

                        @if($recentOrders->isEmpty())
                            <div class="text-center py-8">
                                <p class="text-gray-500 mb-4">You don't have any orders yet</p>
                                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                                    Start shopping
                                </a>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Number</th>
                                            <th class="hidden sm:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sum</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($recentOrders as $order)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <span class="font-medium text-indigo-400">{{ $order->order_number }}</span>
                                                </td>
                                                <td class="hidden sm:table-cell px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $order->created_at->format('d.m.Y') }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <x-shop.order-status-badge :order="$order" />
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                    {{ $order->formatted_total }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm">
                                                    <a href="{{ route('orders.show', $order->order_number) }}" class="text-indigo-400 hover:text-indigo-300 font-medium">
                                                        Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Pravý stĺpec: osobné info + adresy --}}
                <div class="space-y-6">
                    <div class="card bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Personal data</h3>
                            <a href="#profile" class="text-sm text-indigo-400 hover:text-indigo-300">Edit</a>
                        </div>
                        <div class="space-y-2 text-sm">
                            <p><span class="text-gray-500">Name:</span> <span class="font-medium text-gray-900">{{ $user->first_name ?? '-' }}</span></p>
                            <p><span class="text-gray-500">Last Name:</span> <span class="font-medium text-gray-900">{{ $user->last_name ?? '-' }}</span></p>
                            <p><span class="text-gray-500">Email:</span> <span class="font-medium text-gray-900">{{ $user->email }}</span></p>
                            <p><span class="text-gray-500">Phone:</span> <span class="font-medium text-gray-900">{{ $user->phone ?? '-' }}</span></p>
                        </div>
                    </div>

                    {{-- Default shipping --}}
                    <div class="card bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Shipping address</h3>
                            <a href="{{ route('addresses.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">Edit</a>
                        </div>

                        @if($shippingAddress)
                            <div class="text-sm text-gray-600">
                                <p class="font-medium text-gray-900">{{ $shippingAddress->full_name }}</p>
                                <p>{{ $shippingAddress->street }}</p>
                                <p>{{ $shippingAddress->postal_code }} {{ $shippingAddress->city }}</p>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">You have not set a delivery address</p>
                            <a href="{{ route('addresses.create', ['type' => 'shipping']) }}" class="inline-block mt-2 text-sm text-indigo-400 hover:text-indigo-300">
                                + Add address
                            </a>
                        @endif
                    </div>

                    {{-- Default billing --}}
                    <div class="card bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Billing address</h3>
                            <a href="{{ route('addresses.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">Edit</a>
                        </div>

                        @if($billingAddress)
                            <div class="text-sm text-gray-600">
                                @if($billingAddress->company_name)
                                    <p class="font-bold text-gray-900">{{ $billingAddress->company_name }}</p>
                                @endif
                                <p class="font-medium text-gray-900">{{ $billingAddress->full_name }}</p>
                                <p>{{ $billingAddress->street }}</p>
                                <p>{{ $billingAddress->postal_code }} {{ $billingAddress->city }}</p>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">You do not have a billing address set up</p>
                            <a href="{{ route('addresses.create', ['type' => 'billing']) }}" class="inline-block mt-2 text-sm text-indigo-400 hover:text-indigo-300">
                                + Add address
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- =============== PROFIL (Breeze partials) =============== --}}
            <div id="profile" class="mt-10">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Profile settings</h3>

                <div class="space-y-6">
                    <div class="card p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="card p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="card p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
            {{-- ========================================================= --}}
        </div>
    </div>
</x-layouts.default-layout>
