<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

    @include('layouts.navigation') {{-- Usa la navegación de Breeze si existe --}}

    <div class="container mx-auto px-4 py-6">
        @yield('content')  {{-- Aquí se insertará el contenido de cada vista --}}
    </div>

</body>
</html>
