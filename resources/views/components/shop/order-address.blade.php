{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
{{-- [AI] Vygenerovane s pomocou AI (Claude). Riadky dorucovacej adresy objednavky (potvrdenie aj detail objednavky). --}}
@props(['address'])

<address class="not-italic leading-relaxed text-muted">
    <span class="block font-semibold text-text">{{ $address?->full_name }}</span>
    <span class="block">{{ $address?->street }}</span>
    <span class="block">{{ $address?->postal_code }} {{ $address?->city }}</span>
    <span class="mt-2 block">{{ $address?->phone }}</span>
    <span class="block">{{ $address?->email }}</span>
</address>
