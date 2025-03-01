@extends('layouts.app')

@section('title', 'Mis Partidos')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Mis Partidos</h1>
    <a href="{{ route('matches.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">Nuevo Partido</a>

    <table class="w-full mt-4 border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Jornada</th>
                <th class="border p-2">Rival</th>
                <th class="border p-2">Fecha</th>
                <th class="border p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($matches as $match)
                <tr>
                    <td class="border p-2">{{ $match->numero_jornada }}</td>
                    <td class="border p-2">{{ $match->equipo_rival }}</td>
                    <td class="border p-2">{{ $match->fecha_partido }}</td>
                    <td class="border p-2">
                        <a href="{{ route('matches.edit', $match) }}" class="bg-yellow-500 text-white px-3 py-1 rounded">Editar</a>
                        <form action="{{ route('matches.destroy', $match) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
