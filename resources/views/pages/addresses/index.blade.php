{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
{{-- Ulozene adresy pouzivatela (dorucovacie a fakturacne). --}}
@php
    $groups = [
        ['shipping', 'Shipping Addresses', $shippingAddresses, "You don't have any shipping addresses yet", 'Add shipping address',
            'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
        ['billing', 'Billing Addresses', $billingAddresses, "You don't have any billing addresses yet", 'Add billing address',
            'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
    ];
@endphp

<x-layouts.default-layout title="My Addresses">

    <x-ui.page-header eyebrow="My account" title="My Addresses" subtitle="Manage your shipping and billing addresses.">
        <x-slot name="actions">
            <x-button :href="route('dashboard')" variant="ghost">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Dashboard
            </x-button>
        </x-slot>
    </x-ui.page-header>

    <x-ui.flash-messages />

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-8">
        @foreach($groups as [$type, $title, $addresses, $emptyText, $addText, $iconPath])
            <section aria-labelledby="addresses-{{ $type }}">
                <div class="mb-4 flex items-center justify-between gap-4 border-b border-line pb-3">
                    <h2 id="addresses-{{ $type }}" class="flex items-center gap-2.5 text-2xl">
                        <span class="grid size-9 place-items-center rounded-xs bg-ink-900 text-brand-400" aria-hidden="true">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-width="2" d="{{ $iconPath }}"/></svg>
                        </span>
                        {{ $title }}
                        <span class="num text-base text-subtle">({{ $addresses->count() }})</span>
                    </h2>
                    <x-button :href="route('addresses.create', ['type' => $type])" size="sm" aria-label="{{ $addText }}">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Add
                    </x-button>
                </div>

                @if($addresses->isEmpty())
                    <div class="rounded-xs border-2 border-dashed border-line-strong/60 bg-tile p-8 text-center">
                        <svg class="mx-auto mb-3 size-10 text-line-strong" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="square" stroke-width="1.5" d="{{ $iconPath }}"/>
                        </svg>
                        <p class="mb-4 text-muted">{{ $emptyText }}</p>
                        <x-button :href="route('addresses.create', ['type' => $type])" variant="secondary">{{ $addText }} →</x-button>
                    </div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                        @foreach($addresses as $address)
                            <x-shop.address-card :address="$address" />
                        @endforeach
                    </div>
                @endif
            </section>
        @endforeach
    </div>

</x-layouts.default-layout>
