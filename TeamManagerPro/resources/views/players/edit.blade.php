@extends('layouts.app')

@section('title', 'Editar Jugador')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Editar Jugador</h1>
    <form action="{{ route('players.update', $player) }}" method="POST" class="bg-white p-4 shadow rounded">
        @csrf @method('PUT')

        <label class="block">Nombre:</label>
        <input type="text" name="nombre" class="border p-2 w-full" value="{{ $player->nombre }}" required>

        <label class="block mt-2">Apellido:</label>
        <input type="text" name="apellido" class="border p-2 w-full" value="{{ $player->apellido }}" required>

        <label class="block mt-2">Dorsal:</label>
        <input type="number" name="dorsal" class="border p-2 w-full" value="{{ $player->dorsal }}" required>

        <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Actualizar</button>
    </form>
@endsection
