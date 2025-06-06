@extends('layouts.dashboard')
@section('content')

<div class="min-h-screen bg-[#1E293B] text-white font-sans p-4 sm:p-6">
    <!-- Título principal -->
    <h1 class="text-3xl sm:text-4xl font-title text-[#00B140] mb-6 uppercase tracking-wide">
        Gestión de Plantillas
    </h1>

<!-- Botón para mostrar/ocultar formulario -->
<div class="mb-6 flex flex-wrap gap-3">
    <button id="toggle-form-btn" onclick="toggleNewTeamForm()" class="bg-[#00B140] text-white px-3 py-2 rounded-lg hover:brightness-110 text-sm sm:text-base">
        Añadir Nueva Plantilla
    </button>

    <form action="{{ route('teams.demo') }}" method="POST">
        @csrf
        <button type="submit" class="bg-[#FACC15] text-black px-3 py-2 rounded-lg hover:brightness-110 text-sm sm:text-base">
            Crear Plantilla de Demostración
        </button>
    </form>
</div>

<!-- Formulario para crear plantilla (oculto inicialmente) -->
<div id="new-team-form" class="hidden transition-opacity duration-300">
    @include('teams.team_form')
</div>

    <!-- Mensajes de alerta -->
    @include('components.alert')

    <!-- Listado de equipos -->
    <div class="mt-6">
        @foreach ($teams as $team)
            <div class="bg-[#1E3A8A] rounded-lg p-4 sm:p-6 mb-6 shadow-lg">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <h2 class="text-xl sm:text-2xl font-title uppercase tracking-wide text-white">
                        <a href="{{ route('teams.show', $team->id) }}" class="hover:underline text-[#FACC15]">
                            {{ $team->nombre }} ({{ strtoupper($team->modalidad) }})
                        </a>
                    </h2>

                    <!-- Botón eliminar -->
                    <form action="{{ route('teams.destroy', $team->id) }}" method="POST" onsubmit="return confirmDelete(event, '{{ $team->nombre }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-[#EF4444] text-white px-3 py-2 rounded-lg hover:brightness-110 font-sans text-sm sm:text-base">
                            Eliminar
                        </button>
                    </form>
                </div>

                <!-- Formulario oculto de competición -->
                <div id="competicion-form-{{ $team->id }}" class="hidden mt-4 bg-[#111827] p-4 rounded-lg">
                    <h3 class="text-lg font-title text-[#FACC15] uppercase tracking-wide mb-2">Añadir Partido de Liga</h3>
                    <form action="{{ route('rivales_liga.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="team_id" value="{{ $team->id }}">
                        <input type="hidden" name="tipo" value="liga">
                        <input type="number" name="jornada" placeholder="Número de Jornada" required class="w-full p-2 bg-white text-black rounded-lg mb-2 font-sans">
                        <input type="text" name="nombre_equipo" placeholder="Equipo Rival" required class="w-full p-2 bg-white text-black rounded-lg mb-2 font-sans">
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" class="bg-[#00B140] text-white px-4 py-2 rounded-lg hover:brightness-110 font-sans">Guardar</button>
                            <button type="button" onclick="toggleSection('competicion-form-{{ $team->id }}')" class="bg-[#EF4444] text-white px-4 py-2 rounded-lg hover:brightness-110 font-sans">Cancelar</button>
                        </div>
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

document.addEventListener('DOMContentLoaded', () => {
        const alerts = document.querySelectorAll('.alert-success');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add('opacity-0');
                setTimeout(() => alert.remove(), 300); // se elimina tras el fade out
            }, 3000); 
        });
    });

    function toggleNewTeamForm() {
        const form = document.getElementById('new-team-form');
        form.classList.toggle('hidden');
    }
</script>
@endsection
