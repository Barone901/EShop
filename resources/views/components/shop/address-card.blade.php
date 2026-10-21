{{--
    [AI] Vygenerovane s pomocou AI (Claude) - vyclenene zo sablon adries.
    Karta ulozenej adresy v "My Addresses" (s akciami Edit / Set as default / Delete).
    Dorucovacie adresy su v indigo farbe a ukazuju telefon, fakturacne v zelenej a ukazuju firemne udaje.
--}}
@props(['address'])

@php
    $isBilling = $address->type === \App\Models\UserAddress::TYPE_BILLING;

    // Plné názvy tried, aby ich Tailwind našiel pri builde
    $colors = $isBilling
        ? ['default' => 'border-green-500 ring-2 ring-green-100', 'badge' => 'bg-green-100 text-green-800', 'link' => 'text-green-400 hover:text-green-300', 'hover' => 'hover:text-green-400']
        : ['default' => 'border-indigo-500 ring-2 ring-indigo-100', 'badge' => 'bg-indigo-100 text-indigo-800', 'link' => 'text-indigo-400 hover:text-indigo-300', 'hover' => 'hover:text-indigo-400'];
@endphp

<div class="card bg-white rounded-xl shadow-sm border {{ $address->is_default ? $colors['default'] : 'border-gray-200' }} p-6">
    @if($address->is_default)
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors['badge'] }} mb-3">
            <svg class="w-3 h-3 mr-1" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            Default
        </span>
    @endif

    <div class="text-gray-900">
        @if($isBilling && $address->company_name)
            <p class="font-bold text-gray-900">{{ $address->company_name }}</p>
        @endif
        <p class="font-semibold">{{ $address->full_name }}</p>
        <p class="text-gray-600">{{ $address->street }}</p>
        <p class="text-gray-600">{{ $address->postal_code }} {{ $address->city }}</p>
        <p class="text-gray-600">{{ $address->country }}</p>

        @if($isBilling)
            @if($address->ico || $address->dic || $address->ic_dph)
                <div class="mt-2 text-sm text-gray-500">
                    @if($address->ico)<p>Company ID: {{ $address->ico }}</p>@endif
                    @if($address->dic)<p>Tax ID: {{ $address->dic }}</p>@endif
                    @if($address->ic_dph)<p>VAT ID: {{ $address->ic_dph }}</p>@endif
                </div>
            @endif
        @elseif($address->phone)
            <p class="text-gray-500 text-sm mt-2">Phone: {{ $address->phone }}</p>
        @endif
    </div>

    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
        <a href="{{ route('addresses.edit', $address) }}" class="text-sm {{ $colors['link'] }} font-medium">
            Edit
        </a>
        @if(!$address->is_default)
            <span class="text-gray-300">|</span>
            <form action="{{ route('addresses.setDefault', $address) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-600 {{ $colors['hover'] }} font-medium">
                    Set as default
                </button>
            </form>
        @endif
        <span class="text-gray-300">|</span>
        <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline" data-confirm="Are you sure you want to delete this address?">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm text-red-400 hover:text-red-300 font-medium">
                Delete
            </button>
        </form>
    </div>
</div>
