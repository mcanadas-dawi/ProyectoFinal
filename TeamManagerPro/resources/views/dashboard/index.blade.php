@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-purple-600 mb-6">Gestión de Plantillas</h1>

    @include('components.alert')

    <!-- Formulario para crear plantillas -->
    @include('teams.team_form')

    <div class="mt-6">
        @foreach ($teams as $team)
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-3xl font-semibold text-gray-700">
                        <a href="{{ route('teams.show', $team->id) }}" class="text-green-600 hover:underline">
                            {{ $team->nombre }} ({{ strtoupper($team->modalidad) }})
                        </a>
                    </h2>
                    
                    <!-- Botón para eliminar plantilla con confirmación -->
                    <form action="{{ route('teams.destroy', $team->id) }}" method="POST" onsubmit="return confirmDelete(event, '{{ $team->nombre }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg">Eliminar Plantilla</button>
                    </form>
                </div>

                

                    <!-- Sección oculta para agregar competición -->
                    <div id="competicion-form-{{ $team->id }}" class="hidden mt-4 bg-gray-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800">Añadir Partido de Liga</h3>
                        <form action="{{ route('rivales_liga.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="team_id" value="{{ $team->id }}">
                            <input type="hidden" name="tipo" value="liga">
                            <input type="number" name="jornada" placeholder="Número de Jornada" required class="w-full p-2 border rounded-lg mb-2">
                            <input type="text" name="nombre_equipo" placeholder="Equipo Rival" required class="w-full p-2 border rounded-lg mb-2">
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Guardar</button>
                            <button type="button" onclick="toggleSection('competicion-form-{{ $team->id }}')" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancelar</button>
                        </form>
                    </div>  
            </div>
        @endforeach
    </div>
</div>

<script>
function confirmDelete(event, teamName) {
    event.preventDefault();
    if (confirm(`¿Estás seguro de que deseas eliminar la plantilla "${teamName}"? Esta acción no se puede deshacer.`)) {
        event.target.submit();
    }
}
</script>

@endsection
