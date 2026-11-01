{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
{{-- Prevzate z Laravel Breeze (https://github.com/laravel/breeze), upravene. --}}
@props(['messages'])

@if ($messages)
    {{-- Chyba = tmava cervena + ikona (odlisna od brand cervenej) --}}
    <ul {{ $attributes->merge(['class' => 'space-y-1 text-sm text-danger-700']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex gap-1.5"><span aria-hidden="true">⚠</span><span>{{ $message }}</span></li>
        @endforeach
    </ul>
@endif
