@extends('layouts.app')

@section('title', 'Nuevo Partido')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Nuevo Partido</h1>
    <form action="{{ route('matches.store') }}" method="POST" class="bg-white p-4 shadow rounded">
        @csrf
        <label class="block">Selecciona tu equipo:</label>
        <select name="team_id" class="border p-2 w-full">
            @foreach ($teams as $team)
                <option value="{{ $team->id }}">{{ $team->nombre }} ({{ $team->modalidad }})</option>
            @endforeach
        </select>

        <label class="block mt-2">Número de Jornada:</label>
        <input type="number" name="numero_jornada" class="border p-2 w-full" required>

        <label class="block mt-2">Equipo Rival:</label>
        <input type="text" name="equipo_rival" class="border p-2 w-full" required>

        <label class="block mt-2">Fecha del Partido:</label>
        <input type="date" name="fecha_partido" class="border p-2 w-full" required>

        <button type="submit" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">Guardar</button>
    </form>
@endsection
