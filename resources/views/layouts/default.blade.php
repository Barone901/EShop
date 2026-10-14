<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Kodovanie stranky (UTF-8) --}}
    <meta charset="UTF-8">

    {{-- Responsivne nastavenie pre mobily (spravna sirka a zoom) --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF token pre formulare a ajax requesty (ochrana) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Nazov stranky: pouzije $title ak existuje, inak nazov aplikacie z configu --}}
    <title>{{ $title ?? config('app.name', 'Eshop') }}</title>

    {{-- Vite build: tailwind css + app js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Volitelne: extra veci do head (napr. meta tagy, extra css na konkretnu stranku) --}}
    {{ $head ?? '' }}
</head>

{{--
    Body triedy:
    - font-sans + antialiased = krajsie vykreslenie textu
    - min-h-screen = minimalne vyska celej obrazovky
    - flex flex-col = layout do stlpca (header hore, footer dole)
--}}
<body class="font-sans antialiased min-h-screen flex flex-col">

{{-- Header / navbar (spolocny pre vsetky stranky) --}}
@include('layouts.partials.header')

{{-- Prepnutie layoutu podla $type --}}
@if($type === 'guest')
    {{--
        Guest layout:
        - pouziva sa na login/register
        - obsah je vycentrovany
        - uzsia karta, aby to vyzeralo ako formular
    --}}
    <div class="flex-grow flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="card w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{-- $slot = obsah konkretnej stranky --}}
            {{ $slot }}
        </div>
    </div>

@elseif($type === 'app')
    {{--
        App layout:
        - normalne stranky aplikacie
        - moze mat volitelny $header nad obsahom
    --}}
    @isset($header)
        <header class="card bg-white shadow">
            {{-- Rovnaka sirka ako header, footer a obsah (max-w-7xl) --}}
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{--
        Hlavny obsah:
        - max-w-7xl = rovnaka sirka ako header a footer, aby bolo vsetko zarovnane
        - py-8 = jedine zvisle odsadenie obsahu (stranky si nepridavaju vlastne)
        - flex-grow = aby footer ostal dole
    --}}
    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        {{ $slot }}
    </main>

@else
    {{--
        Default layout:
        - $fullWidth = volitelny obsah na celu sirku okna nad hlavnym obsahom (napr. banner slider)
        - main ma rovnaku sirku ako app layout
    --}}
    {{ $fullWidth ?? '' }}

    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        {{ $slot }}
    </main>
@endif

{{-- Footer (spolocny pre vsetky stranky) --}}
@include('layouts.partials.footer')

{{-- Volitelne skripty na konci (napr. JS len pre konkretnu stranku) --}}
{{ $scripts ?? '' }}
</body>
</html>
