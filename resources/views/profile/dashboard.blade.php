{{--
    Prehlad uctu + nastavenia profilu (Breeze partials). Kotva #profile = ciel presmerovania z profile.edit.
--}}
@php
    $quickLinks = [
        [route('orders.index'), 'My orders', 'Order history and status', 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        [route('addresses.index'), 'My addresses', 'Shipping and billing addresses', 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
        ['#profile', 'Profile settings', 'Personal data and password', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
    ];

    $cardTitle = 'text-xl';
    $editLink = 'inline-flex min-h-11 items-center text-sm font-medium text-brand-600 hover:text-brand-700 hover:underline underline-offset-4';
@endphp

<x-layouts.default-layout title="My Profile" type="app">
    <x-slot name="header">
        <span class="mb-4 block h-[3px] w-12 bg-brand-600" aria-hidden="true"></span>
        <p class="eyebrow">My account</p>
        <h1 class="mt-2 text-3xl md:text-4xl">Welcome, {{ $user->full_name }}!</h1>
        <p class="mt-2 text-muted">Here you will find an overview of your orders, addresses, and account settings.</p>
    </x-slot>

    {{-- Rychle odkazy --}}
    <ul class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach($quickLinks as [$url, $title, $text, $iconPath])
            <li>
                <a href="{{ $url }}" class="card group flex h-full items-center gap-4 p-5 transition-colors hover:border-brand-600">
                    <span class="grid size-12 shrink-0 place-items-center rounded-xs bg-ink-900 text-brand-400" aria-hidden="true">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="square" stroke-width="2" d="{{ $iconPath }}"/>
                        </svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block font-display text-lg font-semibold uppercase tracking-wide group-hover:text-brand-600">{{ $title }}</span>
                        <span class="block text-sm text-muted">{{ $text }}</span>
                    </span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Posledne objednavky --}}
        <section class="card lg:col-span-2" aria-labelledby="recent-orders">
            <div class="flex items-center justify-between border-b border-line px-5 py-3 md:px-6">
                <h2 id="recent-orders" class="{{ $cardTitle }}">Latest orders</h2>
                <a href="{{ route('orders.index') }}" class="{{ $editLink }}">Show all →</a>
            </div>

            @if($recentOrders->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="mb-4 text-muted">You don't have any orders yet</p>
                    <x-button :href="route('home')">Start shopping</x-button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-paper">
                            <tr>
                                <th scope="col" class="eyebrow px-5 py-3 text-left md:px-6">Number</th>
                                <th scope="col" class="eyebrow hidden px-4 py-3 text-left sm:table-cell">Date</th>
                                <th scope="col" class="eyebrow px-4 py-3 text-left">Status</th>
                                <th scope="col" class="eyebrow px-4 py-3 text-right">Sum</th>
                                <th scope="col" class="px-5 py-3 md:px-6"><span class="sr-only">Action</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-paper">
                                    <td class="num whitespace-nowrap px-5 py-3 md:px-6">{{ $order->order_number }}</td>
                                    <td class="num hidden whitespace-nowrap px-4 py-3 text-subtle sm:table-cell">{{ $order->created_at->format('d.m.Y') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-shop.order-status-badge :order="$order" /></td>
                                    <td class="num whitespace-nowrap px-4 py-3 text-right">{{ $order->formatted_total }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-right md:px-6">
                                        <a href="{{ route('orders.show', $order->order_number) }}" class="{{ $editLink }}" aria-label="Order {{ $order->order_number }} detail">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- Pravy stlpec: osobne udaje + predvolene adresy --}}
        <div class="space-y-6">
            <section class="card p-5 md:p-6" aria-labelledby="personal-data">
                <div class="mb-3 flex items-center justify-between">
                    <h2 id="personal-data" class="{{ $cardTitle }}">Personal data</h2>
                    <a href="#profile" class="{{ $editLink }}">Edit</a>
                </div>
                <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
                    <dt class="text-subtle">Name</dt><dd class="font-medium">{{ $user->first_name ?? '-' }}</dd>
                    <dt class="text-subtle">Last Name</dt><dd class="font-medium">{{ $user->last_name ?? '-' }}</dd>
                    <dt class="text-subtle">Email</dt><dd class="break-all font-medium">{{ $user->email }}</dd>
                    <dt class="text-subtle">Phone</dt><dd class="num">{{ $user->phone ?? '-' }}</dd>
                </dl>
            </section>

            {{-- Predvolena dorucovacia adresa --}}
            <section class="card p-5 md:p-6" aria-labelledby="default-shipping">
                <div class="mb-3 flex items-center justify-between">
                    <h2 id="default-shipping" class="{{ $cardTitle }}">Shipping address</h2>
                    <a href="{{ route('addresses.index') }}" class="{{ $editLink }}">Edit</a>
                </div>

                @if($shippingAddress)
                    <address class="text-sm not-italic leading-relaxed text-muted">
                        <span class="block font-semibold text-text">{{ $shippingAddress->full_name }}</span>
                        <span class="block">{{ $shippingAddress->street }}</span>
                        <span class="block">{{ $shippingAddress->postal_code }} {{ $shippingAddress->city }}</span>
                    </address>
                @else
                    <p class="text-sm text-muted">You have not set a delivery address</p>
                    <a href="{{ route('addresses.create', ['type' => 'shipping']) }}" class="{{ $editLink }}">+ Add address</a>
                @endif
            </section>

            {{-- Predvolena fakturacna adresa --}}
            <section class="card p-5 md:p-6" aria-labelledby="default-billing">
                <div class="mb-3 flex items-center justify-between">
                    <h2 id="default-billing" class="{{ $cardTitle }}">Billing address</h2>
                    <a href="{{ route('addresses.index') }}" class="{{ $editLink }}">Edit</a>
                </div>

                @if($billingAddress)
                    <address class="text-sm not-italic leading-relaxed text-muted">
                        @if($billingAddress->company_name)
                            <span class="block font-bold text-text">{{ $billingAddress->company_name }}</span>
                        @endif
                        <span class="block font-semibold text-text">{{ $billingAddress->full_name }}</span>
                        <span class="block">{{ $billingAddress->street }}</span>
                        <span class="block">{{ $billingAddress->postal_code }} {{ $billingAddress->city }}</span>
                    </address>
                @else
                    <p class="text-sm text-muted">You do not have a billing address set up</p>
                    <a href="{{ route('addresses.create', ['type' => 'billing']) }}" class="{{ $editLink }}">+ Add address</a>
                @endif
            </section>
        </div>
    </div>

    {{-- =============== NASTAVENIA PROFILU (Breeze partials) =============== --}}
    <section id="profile" class="mt-12 scroll-mt-40" aria-labelledby="profile-settings">
        <x-ui.section-header heading-id="profile-settings" eyebrow="Account" title="Profile settings" />

        <div class="space-y-6">
            <x-ui.card padding="lg">
                @include('profile.partials.update-profile-information-form')
            </x-ui.card>

            <x-ui.card padding="lg">
                @include('profile.partials.update-password-form')
            </x-ui.card>

            <x-ui.card padding="lg" class="border-l-3 border-l-danger-700">
                @include('profile.partials.delete-user-form')
            </x-ui.card>
        </div>
    </section>
</x-layouts.default-layout>
