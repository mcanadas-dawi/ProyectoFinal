@extends('layouts.app')

@section('title', 'Mis Jugadores')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Mis Jugadores</h1>
    <a href="{{ route('players.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">Nuevo Jugador</a>

    <table class="w-full mt-4 border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Nombre</th>
                <th class="border p-2">Dorsal</th>
                <th class="border p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($players as $player)
                <tr>
                    <td class="border p-2">{{ $player->nombre }} {{ $player->apellido }}</td>
                    <td class="border p-2">{{ $player->dorsal }}</td>
                    <td class="border p-2">
                        <a href="{{ route('players.edit', $player) }}" class="bg-yellow-500 text-white px-3 py-1 rounded">Editar</a>
                        <form action="{{ route('players.destroy', $player) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
