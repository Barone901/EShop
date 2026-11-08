{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
{{--
    [AI] Vygenerovane s pomocou AI (Claude) - vyclenene zo sablon adries.
    Karta ulozenej adresy v "My Addresses" (akcie Edit / Set as default / Delete).
    Typ adresy sa rozlisuje ikonou a textom (nie farbou); predvolena adresa ma akcentovy pruh a stitok.
    JS: formular zmazania ma data-confirm (page-actions.js) - nemen.
--}}
@props(['address'])

@php
    /** @var \App\Models\UserAddress $address */

    $isBilling = $address->type === \App\Enums\AddressType::Billing;
    $action = 'inline-flex min-h-11 items-center gap-1.5 rounded-xs px-2 text-sm font-medium transition-colors';
@endphp

<article @class([
    'card flex h-full flex-col p-5',
    'border-l-3 border-l-brand-600' => $address->is_default,
])>
    <div class="mb-3 flex items-start justify-between gap-3">
        <p class="eyebrow flex items-center gap-1.5">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                @if($isBilling)
                    <path stroke-linecap="square" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                @else
                    <path stroke-linecap="square" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                @endif
            </svg>
            {{ $address->type_label }}
        </p>

        @if($address->is_default)
            <x-ui.badge variant="dark">{{ __('✓ Default') }}</x-ui.badge>
        @endif
    </div>

    <address class="flex-1 not-italic leading-relaxed text-muted">
        @if($isBilling && $address->company_name)
            <span class="block font-bold text-text">{{ $address->company_name }}</span>
        @endif
        <span class="block font-semibold text-text">{{ $address->full_name }}</span>
        <span class="block">{{ $address->street }}</span>
        <span class="block">{{ $address->postal_code }} {{ $address->city }}</span>
        <span class="block">{{ $address->country }}</span>

        @if($isBilling)
            @if($address->ico || $address->dic || $address->ic_dph)
                <dl class="mt-3 grid grid-cols-[auto_1fr] gap-x-3 gap-y-0.5 text-sm">
                    @if($address->ico)<dt class="text-subtle">{{ __('Company ID') }}</dt><dd class="num text-text">{{ $address->ico }}</dd>@endif
                    @if($address->dic)<dt class="text-subtle">{{ __('Tax ID') }}</dt><dd class="num text-text">{{ $address->dic }}</dd>@endif
                    @if($address->ic_dph)<dt class="text-subtle">{{ __('VAT ID') }}</dt><dd class="num text-text">{{ $address->ic_dph }}</dd>@endif
                </dl>
            @endif
        @elseif($address->phone)
            <span class="mt-2 block text-sm">{{ __('Phone:') }} <span class="num text-text">{{ $address->phone }}</span></span>
        @endif
    </address>

    <div class="-mx-2 mt-4 flex flex-wrap items-center gap-1 border-t border-line pt-3">
        <a href="{{ route('addresses.edit', $address) }}" class="{{ $action }} text-brand-600 hover:bg-brand-50 hover:text-brand-700">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            {{ __('Edit') }}
        </a>

        @if(!$address->is_default)
            <form action="{{ route('addresses.setDefault', $address) }}" method="POST">
                @csrf
                <button type="submit" class="{{ $action }} text-muted hover:bg-tile hover:text-text">
                    {{ __('Set as default') }}
                </button>
            </form>
        @endif

        <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="ml-auto" data-confirm="{{ __('Are you sure you want to delete this address?') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="{{ $action }} text-danger-700 hover:bg-danger-50">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="square" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                {{ __('Delete') }}
            </button>
        </form>
    </div>
</article>
