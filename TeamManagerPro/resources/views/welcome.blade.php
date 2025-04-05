<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TeamManagerPro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white flex flex-col items-center justify-center min-h-screen">

    {{-- Contenido central --}}
    <div class="text-center px-6">
        <img src="{{ asset('imagenes/TeamManagerPro_Logo.png') }}" alt="Logo TeamManagerPro" class="mx-auto mb-8 w-64 rounded-xl shadow-lg">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-4">Bienvenido a TeamManagerPro</h1>
        <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">Tu aplicación para gestión de equipos de fútbol.</p>

        {{-- Botones de acción --}}
        <div class="flex justify-center gap-4">
            @auth
                <a href="{{ url('/dashboard') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md font-semibold transition duration-200">
                    Ir a la App
                </a>
            @else
                <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md font-semibold transition duration-200">
                    Acceder
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md font-semibold transition duration-200">
                        Registrarse
                    </a>
                @endif
            @endauth
        </div>


</body>
</html>
