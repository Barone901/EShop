{{-- [AI] Upravene s pomocou AI (Claude): redizajn vzhladu (Tailwind triedy, rozlozenie, pristupnost); logika, routy, formulare a name atributy su povodne. --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Kodovanie stranky (UTF-8) --}}
    <meta charset="UTF-8">

    {{-- Responsivne nastavenie pre mobily (spravna sirka a zoom) --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF token pre formulare a ajax requesty (ochrana) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Farba listy prehliadaca na mobile = grafitovy header --}}
    <meta name="theme-color" content="#16191C">

    {{-- Nazov stranky: pouzije $title ak existuje, inak nazov aplikacie z configu --}}
    <title>{{ $title ?? config('app.name', 'Eshop') }}</title>

    {{-- Vite build: tailwind css + app js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Volitelne: extra veci do head (napr. meta tagy, extra css na konkretnu stranku) --}}
    {{ $head ?? '' }}
</head>

{{--
    Hybridny dizajn: tmavy header a footer (theme-dark), svetly obsah (bg-paper).
    flex flex-col + flex-grow na obsahu = footer vzdy dole.
--}}
<body class="flex min-h-screen flex-col bg-paper font-sans text-text antialiased">

{{-- Preskocenie navigacie pre klavesnicu a citacky obrazovky --}}
<a href="#main-content"
   class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[300] focus:bg-brand-600 focus:px-4 focus:py-2 focus:font-display focus:font-semibold focus:uppercase focus:tracking-wider focus:text-white">
    Skip to content
</a>

{{-- Header / navbar (spolocny pre vsetky stranky) --}}
@include('layouts.partials.header')

{{-- Prepnutie layoutu podla $type --}}
@if($type === 'guest')
    {{--
        Guest layout (login, register, overenie emailu):
        uzka biela karta vycentrovana na svetlom pozadi, s akcentovym pruhom navrchu.
    --}}
    <main id="main-content" class="flex flex-grow items-start justify-center px-4 py-10 sm:items-center sm:py-16">
        <div class="card w-full max-w-md border-t-3 border-t-brand-600 p-6 sm:p-8">
            {{-- $slot = obsah konkretnej stranky --}}
            {{ $slot }}
        </div>
    </main>

@elseif($type === 'app')
    {{--
        App layout: stranky uctu, volitelny $header nad obsahom.
    --}}
    @isset($header)
        <div class="border-b border-line bg-white">
            {{-- Rovnaka sirka ako header, footer a obsah (max-w-7xl) --}}
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </div>
    @endisset

    {{-- py-8/10 = jedine zvisle odsadenie obsahu (stranky si nepridavaju vlastne) --}}
    <main id="main-content" class="mx-auto w-full max-w-7xl flex-grow px-4 py-8 sm:px-6 md:py-10 lg:px-8">
        {{ $slot }}
    </main>

@else
    {{--
        Default layout:
        - $fullWidth = volitelny obsah na celu sirku okna nad hlavnym obsahom (napr. banner slider)
        - main ma rovnaku sirku ako app layout
    --}}
    {{ $fullWidth ?? '' }}

    <main id="main-content" class="mx-auto w-full max-w-7xl flex-grow px-4 py-8 sm:px-6 md:py-10 lg:px-8">
        {{ $slot }}
    </main>
@endif

{{-- Footer (spolocny pre vsetky stranky) --}}
@include('layouts.partials.footer')

{{-- Volitelne skripty na konci (napr. JS len pre konkretnu stranku) --}}
{{ $scripts ?? '' }}
</body>
</html>
