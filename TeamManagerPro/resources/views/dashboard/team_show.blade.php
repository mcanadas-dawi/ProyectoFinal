@extends('layouts.dashboard')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        {{ $team->nombre }} ({{ strtoupper($team->modalidad) }})
    </h1>
    <form action="{{ route('teams.destroy', $team->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este equipo? Esta acción no se puede deshacer.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-700">
            Eliminar Equipo
        </button>
    </form>
</div>


    <!-- Sección de Jugadores -->
    <div class="bg-blue-200 shadow-lg rounded-lg p-6 mb-6">
    <div class="flex items-center justify-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-900 flex-grow text-left">Jugadores</h2>
        <button onclick="openAddPlayerModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Añadir Nuevo Jugador
        </button>
        <button onclick="openExistingPlayerModal()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 ml-4">
            Añadir Jugador de Otra Plantilla
        </button>

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
                        <span id="valoracion-{{ $player->id }}">{{ number_format($player->getValoracionPorPlantilla($team->id), 2) }}</span>
                        <input type="number" name="valoracion" step="0.01" class="hidden w-16 p-1 border rounded" id="edit-valoracion-{{ $player->id }}">
                    </td>

                    <td class="p-2 text-center">
                        <button onclick="editPlayer('{{ $player->id }}')" id="edit-btn-{{ $player->id }}" class="bg-yellow-500 text-white px-3 py-1 rounded">Editar</button>
                        <button onclick="savePlayer('{{ $player->id }}')" id="save-btn-{{ $player->id }}" class="hidden bg-green-500 text-white px-3 py-1 rounded">Guardar</button>
                        <form action="{{ route('players.destroy', $player->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este jugador? Esta acción no se puede deshacer.')" class="inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="team_id" value="{{ $team->id }}">
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
    <div class="flex items-center justify-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-900 flex-grow text-left">Partidos</h2>
        <button onclick="openAddMatchModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Añadir Partido
        </button>
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
                <th class="p-2">Convocatoria</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-800">
        @if ($team->matches->isNotEmpty())
    @foreach ($team->matches as $match)
        <tr class="border-b bg-green-100">
            <td class="p-2 text-center">{{ $match->numero_jornada }}</td>
            <td class="p-2 text-center">{{ $match->equipo_rival }}</td>
            
            <td class="p-2 text-center">
                <span id="min-fecha-{{ $match->id }}">{{ $match->fecha_partido }}</span>
                <input type="date" name="fecha_partido" class="hidden w-16 p-1 border rounded" id="edit-fecha-{{ $match->id }}">
            </td>

            <td class="p-2 text-center">
                <span id="min-goles-favor-{{ $match->id }}">{{ $match->goles_a_favor }}</span>
                <input type="number" name="goles_a_favor" class="hidden w-16 p-1 border rounded" id="edit-goles-favor-{{ $match->id }}">
            </td>

            <td class="p-2 text-center">
                <span id="min-goles-contra-{{ $match->id }}">{{ $match->goles_en_contra }}</span>
                <input type="number" name="goles_en_contra" class="hidden w-16 p-1 border rounded" id="edit-goles-contra-{{ $match->id }}">
            </td>

            <td class="p-2 text-center">
                <button onclick="openConvocatoriaModal('{{ $match->id }}')" class="bg-blue-500 text-white px-3 py-1 rounded">
                    Convocatoria
                </button>
            </td>
            <td class="p-2 text-center">
                <button onclick="openAlineador('{{ $match->id }}')" class="bg-indigo-500 text-white px-3 py-1 rounded">
                    Alineador
                </button>
            </td>
            <td class="p-2 text-center">
                <a href="{{ route('matches.ratePlayers', $match->id) }}" class="bg-orange-400 text-white px-3 py-1 rounded block mb-2">
                    Valorar Jugadores 
                </a>

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
        @endif
        
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

            <label class="block text-gray-700 font-semibold">Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="w-full p-2 border rounded mb-2" required>

            <label class="block text-gray-700 font-semibold">Posición</label>
            <select name="posicion" required class="w-full p-2 border rounded-lg mb-2">
                <option value="Portero">Portero</option>
                <option value="Defensa">Defensa</option>
                <option value="Centrocampista">Centrocampista</option>
                <option value="Delantero">Delantero</option>
            </select>

            <label class="block text-gray-700 font-semibold">Perfil</label>
            <select name="perfil" required class="w-full p-2 border rounded-lg mb-2">
                <option value="Diestro">Diestro</option>
                <option value="Zurdo">Zurdo</option>
            </select>

            <div class="flex justify-end space-x-2 mt-4">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Guardar</button>
                <button type="button" onclick="closeAddPlayerModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancelar</button>
            </div>
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
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Alineador Táctico</h2>

        <!-- Seleccionar Formación -->
        <label class="block text-gray-700 font-semibold">Seleccionar Formación:</label>
        <select id="formation-selector" class="w-full p-2 border rounded-lg mb-4" onchange="updateFormation()">
            <option value="">Seleccionar...</option>
            @if ($team->modalidad == 'F5')
                <option value="1-1-2-1">1-1-2-1</option>
                <option value="1-2-1-1">1-2-1-1</option>
            @elseif ($team->modalidad == 'F7')
                <option value="1-3-2-1">1-3-2-1</option>
                <option value="1-2-3-1">1-2-3-1</option>
            @elseif ($team->modalidad == 'F8')
                <option value="1-3-3-1">1-3-3-1</option>
                <option value="1-2-4-1">1-2-4-1</option>
            @elseif ($team->modalidad == 'F11')
                <option value="1-4-4-2">1-4-4-2</option>
                <option value="1-4-3-3">1-4-3-3</option>
                <option value="1-5-3-2">1-5-3-2</option>
            @endif
        </select>

        <!-- Campo de Fútbol -->
        <div id="field-container" class="relative bg-green-500 h-96 flex justify-center items-center">
            <img src="{{ asset('Imagenes/campo_futbol.jpg') }}" alt="Campo de Fútbol" class="w-full h-full object-cover">
            <div id="player-spots" class="absolute inset-0 flex justify-center items-center">
                <!-- Aquí se inyectarán los jugadores según la formación -->
            </div>
        </div>

        <!-- Lista de Suplentes -->
        <h3 class="text-xl font-semibold text-gray-800 mt-4">Suplentes</h3>
        <div id="suplentes-list" class="max-h-40 overflow-y-auto bg-gray-100 p-2 rounded-lg">
            <table class="w-full">
                <thead>
                    <tr class="text-gray-700">
                        <th class="p-2">Nombre</th>
                        <th class="p-2">Dorsal</th>
                        <th class="p-2">Posición</th>
                    </tr>
                </thead>
                <tbody id="suplentes-body"></tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-between">
            <button onclick="saveAlineacion()" class="bg-green-500 text-white px-4 py-2 rounded-lg">Guardar Alineación</button>
            <button onclick="closeAlineador()" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cerrar</button>
        </div>
    </div>
</div>



<!-- Modal para Convocatoria -->
<div id="convocatoriaModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-1/3">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">Seleccionar Jugadores Convocados</h2>
        <form id="convocatoriaForm">
            @csrf
                @if(isset($match))
                    <input type="hidden" name="match_id" value="{{ $match->id }}">
                @endif


            <!-- Lista de jugadores con checkboxes -->
            <div class="max-h-60 overflow-y-auto">
                @foreach ($team->players as $player)
                <div class="flex items-center mb-2">
                    <input type="checkbox" id="player-{{ $player->id }}" name="convocados[]"
                        value="{{ $player->id }}" class="mr-2"
                        {{ in_array($player->id, $convocados) ? 'checked' : '' }}>
                    <label for="player-{{ $player->id }}">{{ $player->nombre }} {{ $player->apellido }}</label>
                </div>
                @endforeach
            </div>

            <div class="mt-4 flex justify-between">
                <button type="button" onclick="saveConvocatoria()" class="bg-green-500 text-white px-4 py-2 rounded-lg">
                    Guardar Convocatoria
                </button>
                <button type="button" onclick="closeConvocatoriaModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Modal para Añadir Jugadores de Otras Plantillas -->
<div id="existingPlayerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-1/3 shadow-lg">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4 text-center">Añadir Jugador Existente</h2>
        <form action="{{ route('players.addToTeam') }}" method="POST">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">

            <div class="mb-4">
                <label for="player_id" class="block text-gray-700 font-semibold">Seleccionar jugador:</label>
                <select name="player_id" id="player_id" class="w-full p-2 border rounded bg-white text-center" required>
                    <option value="">Seleccionar...</option>
                    @foreach ($allPlayers as $player)
                        <option value="{{ $player->id }}">{{ $player->nombre }} {{ $player->apellido }} (DNI: {{ $player->dni }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-between">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    Añadir Jugador
                </button>
                <button type="button" onclick="closeExistingPlayerModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Contenedor oculto con datos en `data-attributes` -->
<div id="alineador-data" 
     data-players='@json($team->players)' 
     data-convocados='@json($convocados)'></div>

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
    // Ocultar el botón "Editar" y mostrar el botón "Guardar"
    document.getElementById('edit-btn-match-' + id).classList.add('hidden');
    document.getElementById('save-btn-match-' + id).classList.remove('hidden');

    // Mostrar inputs y ocultar spans
    document.getElementById('min-fecha-' + id).classList.add('hidden');
    document.getElementById('edit-fecha-' + id).classList.remove('hidden');

    document.getElementById('min-goles-favor-' + id).classList.add('hidden');
    document.getElementById('edit-goles-favor-' + id).classList.remove('hidden');

    document.getElementById('min-goles-contra-' + id).classList.add('hidden');
    document.getElementById('edit-goles-contra-' + id).classList.remove('hidden');

    // Asegurar que los campos sean editables
    document.getElementById('edit-fecha-' + id).removeAttribute('disabled');
    document.getElementById('edit-goles-favor-' + id).removeAttribute('disabled');
    document.getElementById('edit-goles-contra-' + id).removeAttribute('disabled');
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

//CONVOCATORIA
    function openConvocatoriaModal(matchId) {
        document.getElementById('convocatoriaModal').classList.remove('hidden');
    }
    function closeConvocatoriaModal(matchId) {
        document.getElementById('convocatoriaModal').classList.add('hidden');
    }
    function saveConvocatoria() {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let matchId = document.querySelector('input[name="match_id"]').value;
    
    // Obtener jugadores seleccionados
    let seleccionados = [];
    document.querySelectorAll('input[name="convocados[]"]:checked').forEach((checkbox) => {
        seleccionados.push(checkbox.value);
    });

    fetch(`/matches/${matchId}/convocatoria`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ convocados: seleccionados })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeConvocatoriaModal();
        }
    })
    .catch(error => console.error('Error:', error));
}

//PLAYER MODAL
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


    function openModal() {
        document.getElementById('alineadorModal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('alineadorModal').classList.add('hidden');
    }
    
    
    function openExistingPlayerModal() {
        document.getElementById('existingPlayerModal').classList.remove('hidden');
    }

    function closeExistingPlayerModal() {
        document.getElementById('existingPlayerModal').classList.add('hidden');
    }

 // ALINEADOR 

let alineadorData = document.getElementById("alineador-data");
let allPlayers = JSON.parse(alineadorData.dataset.players);
let convocadosPorPartido = JSON.parse(alineadorData.dataset.convocados);
let selectedPlayers = {};
let currentMatchId = null;

function openAlineador(matchId) {
    currentMatchId = matchId;
    document.getElementById('alineadorModal').classList.remove('hidden');
    updateFormation();
}

function closeAlineador() {
    document.getElementById('alineadorModal').classList.add('hidden');
}

function updateFormation() {
    let formation = document.getElementById('formation-selector').value;
    let fieldContainer = document.getElementById('player-spots');
    fieldContainer.innerHTML = "";

    let formations = {
        '1-4-4-2': [[10, 50], [30, 60], [30, 40], [30, 20], [30, 80], [50, 60], [50, 40], [50, 20], [50, 80], [70, 40], [70, 60]],
        '1-4-3-3': [[50, 90], [20, 70], [40, 70], [60, 70], [80, 70], [30, 50], [50, 50], [70, 50], [30, 30], [50, 30], [70, 30]]
    };

    let convocados = convocadosPorPartido[currentMatchId] || [];

    if (formations[formation]) {
        formations[formation].forEach((pos, index) => {
            let playerCircle = document.createElement('div');
            playerCircle.className = 'absolute w-12 h-12 bg-white border border-gray-800 rounded-full flex items-center justify-center text-lg font-bold shadow-md';
            playerCircle.style.top = `${pos[1]}%`;
            playerCircle.style.left = `${pos[0]}%`;
            playerCircle.style.transform = 'translate(-50%, -50%)';
            playerCircle.style.cursor = "pointer";
            playerCircle.setAttribute('data-index', index);

            let select = document.createElement('select');
            select.className = 'border rounded p-1 text-sm absolute';
            select.style.position = "absolute";
            select.style.top = "100%";
            select.style.left = "50%";
            select.style.transform = "translate(-50%, 5px)";
            select.style.display = "none"; // Se oculta hasta que se haga clic

            let defaultOption = document.createElement('option');
            defaultOption.value = "";
            defaultOption.textContent = "Seleccionar";
            select.appendChild(defaultOption);

            convocados.forEach(playerId => {
                let player = allPlayers.find(p => p.id == playerId);
                if (player) {
                    let option = document.createElement('option');
                    option.value = player.id;
                    option.textContent = `${player.dorsal} - ${player.nombre}`;
                    select.appendChild(option);
                }
            });

            // Mostrar select al hacer clic en el círculo
            playerCircle.onclick = function(event) {
                event.stopPropagation();
                let allSelects = document.querySelectorAll("select");
                allSelects.forEach(s => s.style.display = "none"); // Ocultar otros select abiertos
                select.style.display = "block"; // Mostrar el select solo en el círculo clicado
            };

            // Manejar la selección del jugador
            select.onchange = function() {
                let selectedId = this.value;
                selectedPlayers[index] = selectedId;
                updateSuplentes();
                let selectedPlayer = allPlayers.find(p => p.id == selectedId);
                playerCircle.textContent = selectedPlayer ? selectedPlayer.dorsal : "";
                select.style.display = "none"; // Oculta el select tras la selección
            };

            playerCircle.appendChild(select);
            fieldContainer.appendChild(playerCircle);
        });
    }

    updateSuplentes();
}


function updateSuplentes() {
    let suplentesBody = document.getElementById('suplentes-body');
    suplentesBody.innerHTML = "";

    allPlayers.forEach(player => {
        if (convocadosPorPartido[currentMatchId]?.includes(player.id) && !Object.values(selectedPlayers).includes(player.id.toString())) {
            let row = `<tr>
                <td class="p-2">${player.nombre}</td>
                <td class="p-2">${player.dorsal}</td>
                <td class="p-2">${player.posicion}</td>
            </tr>`;
            suplentesBody.innerHTML += row;
        }
    });
}
function saveAlineacion() {
    let alineacion = [];

    Object.keys(selectedPlayers).forEach(index => {
        alineacion.push({
            posicion: index,
            player_id: selectedPlayers[index]
        });
    });

    fetch(`/matches/${currentMatchId}/save-alineacion`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ alineacion })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Alineación guardada correctamente');
            closeAlineador();
        }
    })
    .catch(error => console.error('Error:', error));
}
function loadAlineacion() {
    fetch(`/matches/${currentMatchId}/get-alineacion`)
    .then(response => response.json())
    .then(data => {
        selectedPlayers = {};
        data.forEach(player => {
            selectedPlayers[player.posicion] = player.player_id;
        });
        updateFormation();
    })
    .catch(error => console.error('Error:', error));
}

</script>

@endsection
   