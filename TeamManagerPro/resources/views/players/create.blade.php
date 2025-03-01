@extends('layouts.app')

@section('title', 'Nuevo Jugador')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Nuevo Jugador</h1>
    <form action="{{ route('players.store') }}" method="POST" class="bg-white p-4 shadow rounded">
        @csrf

        <label class="block">Nombre:</label>
        <input type="text" name="nombre" class="border p-2 w-full" required>

        <label class="block mt-2">Apellido:</label>
        <input type="text" name="apellido" class="border p-2 w-full" required>

        <label class="block mt-2">Dorsal:</label>
        <input type="number" name="dorsal" class="border p-2 w-full" required>

        <button type="submit" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">Guardar</button>
    </form>
@endsection
