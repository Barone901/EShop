<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Eshop') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center bg-gray-100 text-gray-800">
    <h1 class="text-3xl font-bold">{{ config('app.name', 'Eshop') }}</h1>
</body>
</html>
