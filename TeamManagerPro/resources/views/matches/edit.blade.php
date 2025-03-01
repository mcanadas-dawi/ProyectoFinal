@extends('layouts.app')

@section('title', 'Editar Partido')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Editar Partido</h1>
    <form action="{{ route('matches.update', $match) }}" method="POST" class="bg-white p-4 shadow rounded">
        @csrf @method('PUT')

        <label class="block">Resultado:</label>
        <select name="resultado" class="border p-2 w-full">
            <option value="">Pendiente</option>
            <option value="Victoria" {{ $match->resultado == 'Victoria' ? 'selected' : '' }}>Victoria</option>
            <option value="Empate" {{ $match->resultado == 'Empate' ? 'selected' : '' }}>Empate</option>
            <option value="Derrota" {{ $match->resultado == 'Derrota' ? 'selected' : '' }}>Derrota</option>
        </select>

        <label class="block mt-2">Goles a favor:</label>
        <input type="number" name="goles_a_favor" class="border p-2 w-full" value="{{ $match->goles_a_favor }}">

        <label class="block mt-2">Goles en contra:</label>
        <input type="number" name="goles_en_contra" class="border p-2 w-full" value="{{ $match->goles_en_contra }}">

        <label class="block mt-2">Actuación del equipo (1-10):</label>
        <input type="number" name="actuacion_equipo" min="1" max="10" class="border p-2 w-full" value="{{ $match->actuacion_equipo }}">

        <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Actualizar</button>
    </form>
@endsection
