@extends('layouts.app')

@section('title', 'Mis Plantillas')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Mis Plantillas</h1>
    <a href="{{ route('teams.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">Nueva Plantilla</a>

    <table class="w-full mt-4 border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Nombre</th>
                <th class="border p-2">Modalidad</th>
                <th class="border p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($teams as $team)
                <tr>
                    <td class="border p-2">{{ $team->nombre }}</td>
                    <td class="border p-2">{{ $team->modalidad }}</td>
                    <td class="border p-2">
                        <a href="{{ route('teams.edit', $team) }}" class="bg-yellow-500 text-white px-3 py-1 rounded">Editar</a>
                        <form action="{{ route('teams.destroy', $team) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
