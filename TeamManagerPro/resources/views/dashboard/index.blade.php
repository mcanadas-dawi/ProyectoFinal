<!-- resources/views/dashboard/index.blade.php -->
@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Gestión de Plantillas</h1>

    @include('components.alert')

    <!-- Formulario para crear plantillas -->
    @include('dashboard.team_form')

    <div class="mt-6">
        @foreach ($teams as $team)
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-700">
                    <a href="{{ route('teams.show', $team->id) }}" class="text-blue-500 hover:underline">
                        {{ $team->nombre }} ({{ strtoupper($team->modalidad) }})
                    </a>
                </h2>
                    
                    <!-- Botón para eliminar plantilla con confirmación -->
                    <form action="{{ route('teams.destroy', $team->id) }}" method="POST" onsubmit="return confirmDelete(event, '{{ $team->nombre }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg">Eliminar</button>
                    </form>
                </div>
                
                <!-- Contenedor de botones dentro del recuadro de la plantilla -->
                @if ($team->exists)
                    <div class="mt-4 flex space-x-4">
                        <button onclick="toggleSection('player-form-{{ $team->id }}')" 
                                class="bg-green-500 text-white px-4 py-2 rounded-lg">Añadir Jugadores</button>
                        <button onclick="toggleSection('match-form-{{ $team->id }}')" 
                                class="bg-red-500 text-white px-4 py-2 rounded-lg">Añadir Partidos</button>
                    </div>
                
                    <!-- Sección oculta para agregar jugadores -->
                    <div id="player-form-{{ $team->id }}" class="hidden mt-4">
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800">Añadir Jugador</h3>
                            <form action="{{ route('players.store') }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="team_id" value="{{ $team->id }}">
                                <input type="text" name="nombre" placeholder="Nombre" required
                                    class="w-full p-2 border rounded-lg mb-2">
                                <input type="text" name="apellido" placeholder="Apellido" required
                                    class="w-full p-2 border rounded-lg mb-2">
                                <input type="number" name="dorsal" placeholder="Dorsal" required
                                    class="w-full p-2 border rounded-lg mb-2">
                                <input type="date" name="fecha_nacimiento" required
                                    class="w-full p-2 border rounded-lg mb-2">
                                <select name="posicion" required class="w-full p-2 border rounded-lg mb-2">
                                    <option value="Portero">Portero</option>
                                    <option value="Defensa">Defensa</option>
                                    <option value="Centrocampista">Centrocampista</option>
                                    <option value="Delantero">Delantero</option>
                                </select>
                                <select name="perfil" required class="w-full p-2 border rounded-lg mb-2">
                                    <option value="Diestro">Diestro</option>
                                    <option value="Zurdo">Zurdo</option>
                                    <option value="Ambidiestro">Ambidiestro</option>
                                </select>
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Añadir Jugador</button>
                            </form>
                        </div>
                        @include('dashboard.player_list', ['team' => $team])
                    </div>
                
                    <!-- Sección oculta para agregar partidos -->
                    <div id="match-form-{{ $team->id }}" class="hidden mt-4">
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800">Añadir Partido</h3>
                            <form action="{{ route('matches.store') }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="team_id" value="{{ $team->id }}">
                                <input type="number" name="numero_jornada" placeholder="Número de Jornada" required
                                    class="w-full p-2 border rounded-lg mb-2">
                                <input type="text" name="equipo_rival" placeholder="Equipo Rival" required
                                    class="w-full p-2 border rounded-lg mb-2">
                                <input type="date" name="fecha_partido" required
                                    class="w-full p-2 border rounded-lg mb-2">
                                <input type="hidden" name="goles_a_favor" value="0">
                                <input type="hidden" name="goles_en_contra" value="0">
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg">Añadir Partido</button>
                            </form>
                        </div>
                        @include('dashboard.match_list', ['team' => $team])
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<script>
    function toggleSection(id) {
        var section = document.getElementById(id);
        if (section.classList.contains('hidden')) {
            section.classList.remove('hidden');
        } else {
            section.classList.add('hidden');
        }
    }

    function confirmDelete(event, teamName) {
        event.preventDefault();
        if (confirm(`¿Estás seguro de que deseas eliminar la plantilla "${teamName}"? Esta acción no se puede deshacer.`)) {
            event.target.submit();
        }
    }
</script>
@endsection