@extends('layouts.app')

@section('title', 'Mis Plantillas')

@section('content')
    <div class="max-w-4xl mx-auto">
        
        <h1 class="text-6xl font-extrabold text-gray-800 mb-6 text-center ">⚽ Mis Plantillas</h1>
        <div class="mb-4">
            <a href="{{ route('teams.create') }}" class="flex items-center text-green-600 font-semibold px-4 py-2 rounded-lg hover:text-green-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Plantilla
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-500 to-blue-700 text-white">
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-left">Modalidad</th>
                        <th class="p-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($teams as $team)
                        <tr class="border-b hover:bg-gray-100 transition">
                            <td class="p-4 text-gray-800 font-medium">{{ $team->nombre }}</td>
                            <td class="p-4 text-gray-600">{{ $team->modalidad }}</td>
                            <td class="p-4 flex justify-center space-x-2">
                                <a href="{{ route('teams.edit', $team) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-yellow-600 transform hover:scale-105 transition">
                                    ✏️ Editar
                                </a>
                                <form action="{{ route('teams.destroy', $team) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta plantilla?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-red-600 transform hover:scale-105 transition">
                                        🗑️ Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
