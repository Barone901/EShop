{{-- [AI] Vygenerovane s pomocou AI (Claude). Riadky dorucovacej adresy objednavky (potvrdenie aj detail objednavky). --}}
@props(['address'])

<p class="font-medium text-gray-900">{{ $address?->full_name }}</p>
<p>{{ $address?->street }}</p>
<p>{{ $address?->postal_code }} {{ $address?->city }}</p>
<p class="mt-2">{{ $address?->phone }}</p>
<p>{{ $address?->email }}</p>
