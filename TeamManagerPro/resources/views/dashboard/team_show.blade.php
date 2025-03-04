<!-- resources/views/dashboard/team_show.blade.php -->
@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ $team->nombre }} ({{ strtoupper($team->modalidad) }})</h1>

    <!-- Botón para eliminar equipo -->
    <form action="{{ route('teams.destroy', $team->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este equipo? Esta acción no se puede deshacer.')" class="mb-4 text-right">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg">Eliminar Equipo</button>
    </form>

    <!-- AÑADIR JUGADORES DE OTRAS PLANTILLAS-->
    <div class="bg-purple-200 shadow-lg rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4 text-center">Añadir Jugador Existente</h2>
        <form action="{{ route('players.addToTeam') }}" method="POST">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            <div class="mb-4">
                <label for="player_id" class="block text-gray-700 font-semibold">Seleccionar jugador procedente de otra plantilla:</label>
                <select name="player_id" id="player_id" class="w-full p-2 border rounded bg-white text-center" required>
                    <option value=""> Seleccionar </option>
                    @foreach ($allPlayers as $player)
                    @if(!$team->players->contains($player->id)) <!-- Evita mostrar jugadores ya en la plantilla -->
                    <option value="{{ $player->id }}">{{ $player->nombre }} {{ $player->apellido }} (DNI: {{ $player->dni }})</option>
                    @endif
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">Añadir Jugador</button>
        </form>
    </div>

    <!-- Sección de Jugadores -->
    <div class="bg-blue-200 shadow-lg rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-gray-900">Jugadores</h2>
            <button onclick="openAddPlayerModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Añadir Jugador</button>
        </div>
        <table class="w-full text-center border-collapse">
            <thead class="bg-blue-300 text-gray-900">
                <tr class="border-b">
                    <th class="p-2">Nombre</th>
                    <th class="p-2">Apellido</th>
                    <th class="p-2">DNI</th>
                    <th class="p-2">Dorsal</th>
                    <th class="p-2">Fecha Nac.</th>
                    <th class="p-2">Posición</th>
                    <th class="p-2">Perfil</th>
                    <th class="p-2">Minutos Jugados</th>
                    <th class="p-2">Goles</th>
                    <th class="p-2">Asistencias</th>
                    <th class="p-2">Titular</th>
                    <th class="p-2">Suplente</th>
                    <th class="p-2">Valoración</th>
                    <th class="p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($team->players as $player)
                <tr class="border-b bg-blue-100" id="player-row-{{ $player->id }}">
                    <td class="p-2">{{ $player->nombre }}</td>
                    <td class="p-2">{{ $player->apellido }}</td>
                    <td class="p-2">{{ $player->dni }}</td>
                    <td class="p-2">{{ $player->dorsal }}</td>
                    <td class="p-2">{{ $player->fecha_nacimiento }}</td>
                    <td class="p-2">
                        <span id="pos-{{ $player->id }}">{{ $player->posicion }}</span>
                        <select name="posicion" class="hidden w-full p-1 border rounded" id="edit-pos-{{ $player->id }}">
                            <option value="Portero">Portero</option>
                            <option value="Defensa">Defensa</option>
                            <option value="Centrocampista">Centrocampista</option>
                            <option value="Delantero">Delantero</option>
                        </select>
                    </td>
                    <td class="p-2">
                        <span id="perfil-{{ $player->id }}">{{ $player->perfil }}</span>
                        <select name="perfil" class="hidden w-full p-1 border rounded" id="edit-perfil-{{ $player->id }}">
                            <option value="Diestro">Diestro</option>
                            <option value="Zurdo">Zurdo</option>
                        </select>
                    </td>
                    <td class="p-2">
                        <span id="min-{{ $player->id }}">{{ $player->minutos_jugados }}</span>
                        <input type="number" name="minutos_jugados" class="hidden w-16 p-1 border rounded" id="edit-min-{{ $player->id }}">
                    </td>
                    <td class="p-2">
                        <span id="goles-{{ $player->id }}">{{ $player->goles }}</span>
                        <input type="number" name="goles" class="hidden w-16 p-1 border rounded" id="edit-goles-{{ $player->id }}">
                    </td>
                    <td class="p-2">
                        <span id="asistencias-{{ $player->id }}">{{ $player->asistencias }}</span>
                        <input type="number" name="asistencias" class="hidden w-16 p-1 border rounded" id="edit-asistencias-{{ $player->id }}">
                    </td>
                    <td class="p-2">
                        <span id="titular-{{ $player->id }}">{{ $player->titular }}</span>
                        <input type="number" name="titular" class="hidden w-16 p-1 border rounded" id="edit-titular-{{ $player->id }}">
                    </td>
                    <td class="p-2">
                        <span id="suplente-{{ $player->id }}">{{ $player->suplente }}</span>
                        <input type="number" name="suplente" class="hidden w-16 p-1 border rounded" id="edit-suplente-{{ $player->id }}">
                    </td>
                    <td class="p-2">
                        <span id="valoracion-{{ $player->id }}">{{ $player->valoracion }}</span>
                        <input type="number" name="valoracion" class="hidden w-16 p-1 border rounded" id="edit-valoracion-{{ $player->id }}">
                    </td>
                    <td class="p-2 text-center">
                        <button onclick="editPlayer('{{ $player->id }}')" id="edit-btn-{{ $player->id }}" class="bg-yellow-500 text-white px-3 py-1 rounded">Editar</button>
                        <button onclick="savePlayer('{{ $player->id }}')" id="save-btn-{{ $player->id }}" class="hidden bg-green-500 text-white px-3 py-1 rounded">Guardar</button>
                        <form action="{{ route('players.destroy', $player->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este jugador? Esta acción no se puede deshacer.')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<!-- Sección de Partidos -->
<div class="bg-green-200 shadow-lg rounded-lg p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-900">Partidos</h2>
        <button onclick="openAddMatchModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Añadir Partido</button>
    </div>

    <table class="w-full text-center border-collapse bg-white rounded-lg">
        <thead class="bg-green-300 text-gray-900">
            <tr class="border-b">
                <th class="p-2">Jornada</th>
                <th class="p-2">Equipo Rival</th>
                <th class="p-2">Fecha</th>
                <th class="p-2">Goles a Favor</th>
                <th class="p-2">Goles en Contra</th>
                <th class="p-2">Alineación</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-800">
            @foreach ($team->matches as $match)
            <tr class="border-b bg-green-100">
                <td class="p-2 text-center">{{ $match->numero_jornada }}</td>
                <td class="p-2 text-center">{{ $match->equipo_rival }}</td>
                <td class="p-2 text-center">{{ $match->fecha_partido }}</td>
                <td class="p-2 text-center">{{ $match->goles_a_favor }}</td>
                <td class="p-2 text-center">{{ $match->goles_en_contra }}</td>
                <td class="p-2 text-center">
                    <button onclick="openAlineador('{{ $match->id }}')" class="bg-indigo-500 text-white px-3 py-1 rounded">
                        Alineador
                    </button>
                </td>
                <td class="p-2 text-center">
                    <button onclick="editMatch('{{ $match->id }}')" id="edit-btn-match-{{ $match->id }}" class="bg-yellow-500 text-white px-3 py-1 rounded">Editar</button>
                    <button onclick="saveMatch('{{ $match->id }}')" id="save-btn-match-{{ $match->id }}" class="hidden bg-green-500 text-white px-3 py-1 rounded">Guardar</button>
                    <form action="{{ route('matches.destroy', $match->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este partido? Esta acción no se puede deshacer.')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Modal para Añadir Jugador -->
<div id="addPlayerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-1/3">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Añadir Jugador</h2>
        <form action="{{ route('players.store') }}" method="POST">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            <input type="text" name="nombre" placeholder="Nombre" class="w-full p-2 border rounded mb-2" required>
            <input type="text" name="apellido" placeholder="Apellido" class="w-full p-2 border rounded mb-2" required>
            <input type="text" name="dni" placeholder="DNI" class="w-full p-2 border rounded mb-2" required>
            <input type="number" name="dorsal" placeholder="Dorsal" class="w-full p-2 border rounded mb-2" required>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Guardar</button>
            <button type="button" onclick="closeAddPlayerModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancelar</button>
        </form>
    </div>
</div>

<!-- Modal para Añadir Partido -->
<div id="addMatchModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-1/3">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Añadir Partido</h2>
        <form action="{{ route('matches.store') }}" method="POST">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            <input type="number" name="numero_jornada" placeholder="Jornada" class="w-full p-2 border rounded mb-2" required>
            <input type="text" name="equipo_rival" placeholder="Equipo Rival" class="w-full p-2 border rounded mb-2" required>
            <input type="date" name="fecha_partido" class="w-full p-2 border rounded mb-2" required>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Guardar</button>
            <button type="button" onclick="closeAddMatchModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancelar</button>
        </form>
    </div>
</div>

<!-- Modal del Alineador -->
<div id="alineadorModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-3/4">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Alineador Táctico</h2>
        <p class="text-gray-600 mb-4">Selecciona la alineación para el partido.</p>
        
        <select id="formation-selector-modal" class="w-full p-2 border rounded-lg mb-2 bg-white">
            <option value="1-4-4-2">1-4-4-2</option>
            <option value="1-4-3-3">1-4-3-3</option>
            <option value="1-5-3-2">1-5-3-2</option>
            <option value="libre">Libre</option>
        </select>

        <div id="field-container-modal" class="bg-green-500 h-96 flex justify-center items-center">
            <img src="{{ asset('Imagenes/campo_futbol.jpg') }}" alt="Campo de Fútbol" class="w-full h-full object-cover">
        </div>

        <div class="mt-4 flex justify-end">
            <button onclick="closeAlineador()" class="bg-red-500 text-white px-4 py-2 rounded-lg">Cerrar</button>
        </div>
    </div>
</div>


<script>
    function editPlayer(id) {
        document.getElementById('edit-btn-' + id).classList.add('hidden');
        document.getElementById('save-btn-' + id).classList.remove('hidden');

        document.getElementById('pos-' + id).classList.add('hidden');
        document.getElementById('edit-pos-' + id).classList.remove('hidden');

        document.getElementById('perfil-' + id).classList.add('hidden');
        document.getElementById('edit-perfil-' + id).classList.remove('hidden');

        document.getElementById('min-' + id).classList.add('hidden');
        document.getElementById('edit-min-' + id).classList.remove('hidden');

        document.getElementById('goles-' + id).classList.add('hidden');
        document.getElementById('edit-goles-' + id).classList.remove('hidden');

        document.getElementById('asistencias-' + id).classList.add('hidden');
        document.getElementById('edit-asistencias-' + id).classList.remove('hidden');

        document.getElementById('titular-' + id).classList.add('hidden');
        document.getElementById('edit-titular-' + id).classList.remove('hidden');

        document.getElementById('suplente-' + id).classList.add('hidden');
        document.getElementById('edit-suplente-' + id).classList.remove('hidden');

        document.getElementById('valoracion-' + id).classList.add('hidden');
        document.getElementById('edit-valoracion-' + id).classList.remove('hidden');

    }

    function savePlayer(id) {
        let csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        let posicion = document.getElementById('edit-pos-' + id).value;
        let perfil = document.getElementById('edit-perfil-' + id).value;
        let minutos = document.getElementById('edit-min-' + id).value;
        let goles = document.getElementById('edit-goles-' + id).value;
        let asistencias = document.getElementById('edit-asistencias-' + id).value;
        let titular = document.getElementById('edit-titular-' + id).value;
        let suplente = document.getElementById('edit-suplente-' + id).value;
        let valoracion = document.getElementById('edit-valoracion-' + id).value;
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = `/players/${id}`;

        form.appendChild(createHiddenInput('_token', csrfToken));
        form.appendChild(createHiddenInput('_method', 'PATCH'));
        form.appendChild(createHiddenInput('posicion', posicion));
        form.appendChild(createHiddenInput('perfil', perfil));
        form.appendChild(createHiddenInput('minutos_jugados', minutos));
        form.appendChild(createHiddenInput('goles', goles));
        form.appendChild(createHiddenInput('asistencias', asistencias));
        form.appendChild(createHiddenInput('titular', titular));
        form.appendChild(createHiddenInput('suplente', suplente));
        form.appendChild(createHiddenInput('valoracion', valoracion));

        document.body.appendChild(form);
        form.submit();
    }

    function createHiddenInput(name, value) {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }

    function editMatch(id) {
        document.getElementById('edit-btn-match-' + id).classList.add('hidden');
        document.getElementById('save-btn-match-' + id).classList.remove('hidden');

        document.getElementById('fecha-' + id).classList.add('hidden');
        document.getElementById('edit-fecha-' + id).classList.remove('hidden');

        document.getElementById('goles-favor-' + id).classList.add('hidden');
        document.getElementById('edit-goles-favor-' + id).classList.remove('hidden');

        document.getElementById('goles-contra-' + id).classList.add('hidden');
        document.getElementById('edit-goles-contra-' + id).classList.remove('hidden');
    }

    function saveMatch(id) {
        let csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        let fecha = document.getElementById('edit-fecha-' + id).value;
        let golesFavor = document.getElementById('edit-goles-favor-' + id).value;
        let golesContra = document.getElementById('edit-goles-contra-' + id).value;

        let form = document.createElement('form');
        form.method = 'POST';
        form.action = `/matches/${id}`;

        form.appendChild(createHiddenInput('_token', csrfToken));
        form.appendChild(createHiddenInput('_method', 'PATCH'));
        form.appendChild(createHiddenInput('fecha_partido', fecha));
        form.appendChild(createHiddenInput('goles_a_favor', golesFavor));
        form.appendChild(createHiddenInput('goles_en_contra', golesContra));

        document.body.appendChild(form);
        form.submit();
    }

    function openAlineador(matchId) {
        document.getElementById('alineadorModal').classList.remove('hidden');
        console.log("Abriendo alineador para el partido ID:", matchId);
    }

    function closeAlineador() {
        document.getElementById('alineadorModal').classList.add('hidden');
    }

    function openAddPlayerModal() {
        document.getElementById('addPlayerModal').classList.remove('hidden');
    }

    function closeAddPlayerModal() {
        document.getElementById('addPlayerModal').classList.add('hidden');
    }

    function openAddMatchModal() {
        document.getElementById('addMatchModal').classList.remove('hidden');
    }

    function closeAddMatchModal() {
        document.getElementById('addMatchModal').classList.add('hidden');
    }
</script>



@endsection