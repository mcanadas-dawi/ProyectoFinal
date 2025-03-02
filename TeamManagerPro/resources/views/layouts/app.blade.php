<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TeamManagerPro')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between">
            <a href="{{ route('teams.index') }}" class="text-xl font-bold">TeamManagerPro</a>
            <div>
                <a href="{{ route('teams.index') }}" class="mx-2">Plantillas</a>
                <a href="{{ route('players.index') }}" class="mx-2">Jugadores</a>
                <a href="{{ route('matches.index') }}" class="mx-2">Partidos</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-500 px-3 py-1 rounded">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="container mx-auto mt-5">
        @yield('content')
    </main>
</body>
</html>
