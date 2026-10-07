<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Kodovanie stranky (UTF-8) --}}
    <meta charset="UTF-8">

    {{-- Responsivne nastavenie pre mobily (spravna sirka a zoom) --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF token pre formulare --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Nazov stranky: pouzije $title ak existuje, inak nazov aplikacie z configu --}}
    <title>{{ $title ?? config('app.name', 'Eshop') }}</title>

    {{-- Vite build: tailwind css + app js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased min-h-screen flex flex-col bg-gray-100 text-gray-800">

{{-- Header / navbar (spolocny pre vsetky stranky) --}}
@include('layouts.partials.header')

@if($type === 'guest')
    {{-- Guest layout: login/register, vycentrovana uzsia karta --}}
    <div class="flex-grow flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>

@elseif($type === 'app')
    {{-- App layout: volitelny $header nad obsahom --}}
    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        {{ $slot }}
    </main>

@else
    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        {{ $slot }}
    </main>
@endif
</body>
</html>
