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
                    <th class="p-2">Goles / Goles Encajados</th>
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
                            <option value="Portero" @selected($player->posicion == 'Portero')>Portero</option>
                            <option value="Defensa" @selected($player->posicion == 'Defensa')>Defensa</option>
                            <option value="Centrocampista" @selected($player->posicion == 'Centrocampista')>Centrocampista</option>
                            <option value="Delantero" @selected($player->posicion == 'Delantero')>Delantero</option>
                        </select>
                    </td>

                    <td class="p-2">
                        <span id="perfil-{{ $player->id }}">{{ $player->perfil }}</span>
                        <select name="perfil" class="hidden w-full p-1 border rounded" id="edit-perfil-{{ $player->id }}">
                            <option value="Diestro" @selected($player->perfil == 'Diestro')>Diestro</option>
                            <option value="Zurdo" @selected($player->perfil == 'Zurdo')>Zurdo</option>
                        </select>
                    </td>

                    <td class="p-2">
                        <span id="min-{{ $player->id }}">{{ $player->minutos_jugados }}</span>
                        <input type="number" name="minutos_jugados" class="hidden w-16 p-1 border rounded" id="edit-min-{{ $player->id }}" value="{{ $player->minutos_jugados }}">
                    </td>

                    <td class="p-2">
                        @if ($player->posicion == 'Portero')
                            <span id="goles-{{ $player->id }}">{{ $player->goles_encajados }}</span>
                            <input type="number" name="goles_encajados" class="hidden w-16 p-1 border rounded" id="edit-goles-{{ $player->id }}" value="{{ $player->goles_encajados }}">
                        @else
                            <span id="goles-{{ $player->id }}">{{ $player->goles }}</span>
                            <input type="number" name="goles" class="hidden w-16 p-1 border rounded" id="edit-goles-{{ $player->id }}" value="{{ $player->goles }}">
                        @endif
                    </td>

                    <td class="p-2">
                        <span id="asistencias-{{ $player->id }}">{{ $player->asistencias }}</span>
                        <input type="number" name="asistencias" class="hidden w-16 p-1 border rounded" id="edit-asistencias-{{ $player->id }}" value="{{ $player->asistencias }}">
                    </td>

                    <td class="p-2">
                        <span id="titular-{{ $player->id }}">{{ $player->titular }}</span>
                        <input type="number" name="titular" class="hidden w-16 p-1 border rounded" id="edit-titular-{{ $player->id }}" value="{{ $player->titular }}">
                    </td>

                    <td class="p-2">
                        <span id="suplente-{{ $player->id }}">{{ $player->suplente }}</span>
                        <input type="number" name="suplente" class="hidden w-16 p-1 border rounded" id="edit-suplente-{{ $player->id }}" value="{{ $player->suplente }}">
                    </td>

                    <td class="p-2">
                        <span id="valoracion-{{ $player->id }}">{{ number_format($player->getValoracionPorPlantilla($team->id), 2) }}</span>
                    </td>


                    <td class="p-2 text-center">
                        <!-- Botón Editar -->
                        <button onclick="editPlayer('{{ $player->id }}')" id="edit-btn-{{ $player->id }}" class="bg-yellow-500 text-white px-3 py-1 rounded">Editar</button>

                        <!-- Botón Guardar (oculto inicialmente) -->
                        <button onclick="savePlayer('{{ $player->id }}')" id="save-btn-{{ $player->id }}" class="hidden bg-green-500 text-white px-3 py-1 rounded">Guardar</button>

                        <!-- Botón Cancelar (oculto inicialmente) -->
                        <button onclick="cancelEditPlayer('{{ $player->id }}')" id="cancel-btn-{{ $player->id }}" class="hidden bg-gray-500 text-white px-3 py-1 rounded">Cancelar</button>

                        <!-- Botón Eliminar (visible inicialmente, oculto en modo edición) -->
                        <form action="{{ route('players.destroy', $player->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este jugador? Esta acción no se puede deshacer.')" id="delete-form-{{ $player->id }}" class="inline">
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
        <th class="p-2">Resultado</th>
        <th class="p-2">Actuación del Equipo</th>
        <th class="p-2">Alineación</th>
        <th class="p-2">Convocatoria</th>
        <th class="p-2">Acciones</th>
    </tr>
</thead>
<tbody class="text-gray-800">
    @foreach ($team->matches as $match)
        <tr class="border-b bg-green-100">
            <td class="p-2 text-center">{{ $match->numero_jornada }}</td>
            <td class="p-2 text-center">{{ $match->equipo_rival }}</td>
            
            <td class="p-2 text-center">
                <span id="fecha-{{ $match->id }}">{{ $match->fecha_partido }}</span>
                <input type="date" name="fecha_partido" class="hidden w-16 p-1 border rounded" id="edit-fecha-{{ $match->id }}" value="{{ $match->fecha_partido }}">
            </td>

            <td class="p-2 text-center">
                <span id="goles-favor-{{ $match->id }}">{{ $match->goles_a_favor }}</span>
                <input type="number" name="goles_a_favor" class="hidden w-16 p-1 border rounded" id="edit-goles-favor-{{ $match->id }}" value="{{ $match->goles_a_favor }}">
            </td>

            <td class="p-2 text-center">
                <span id="goles-contra-{{ $match->id }}">{{ $match->goles_en_contra }}</span>
                <input type="number" name="goles_en_contra" class="hidden w-16 p-1 border rounded" id="edit-goles-contra-{{ $match->id }}" value="{{ $match->goles_en_contra }}">
            </td>

            <!-- Resultado -->
            <td class="p-2 text-center">
                <span id="resultado-{{ $match->id }}">{{ $match->resultado ?? 'N/A' }}</span>
                <select name="resultado" class="hidden w-full p-1 border rounded" id="edit-resultado-{{ $match->id }}">
                    <option value="" @selected(is_null($match->resultado))>Seleccionar</option>
                    <option value="Victoria" @selected($match->resultado == 'Victoria')>Victoria</option>
                    <option value="Empate" @selected($match->resultado == 'Empate')>Empate</option>
                    <option value="Derrota" @selected($match->resultado == 'Derrota')>Derrota</option>
                </select>
            </td>

            <!-- Actuación del Equipo -->
            <td class="p-2 text-center">
                <span id="actuacion-{{ $match->id }}">{{ $match->actuacion_equipo !== null ? number_format($match->actuacion_equipo, 2) : 'N/A' }}</span>
                <input type="number" name="actuacion_equipo" step="0.01" min="0" max="10" class="hidden w-16 p-1 border rounded" id="edit-actuacion-{{ $match->id }}" value="{{ $match->actuacion_equipo }}">
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
                <button onclick="cancelEditMatch('{{ $match->id }}')" id="cancel-btn-match-{{ $match->id }}" class="hidden bg-gray-500 text-white px-3 py-1 rounded">Cancelar</button>

                <form action="{{ route('matches.destroy', $match->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este partido? Esta acción no se puede deshacer.')" id="delete-form-match-{{ $match->id }}" class="inline">
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
    <div class="bg-white rounded-lg p-6 w-3/4 max-h-[90vh] overflow-y-auto flex flex-col">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Alineador Táctico</h2>

        <!-- Seleccionar Formación -->
        <label class="block text-gray-700 font-semibold">Seleccionar Formación:</label>
        <select id="formation-selector" class="w-full p-2 border rounded-lg mb-4" onchange="updateFormation()">
            <option value="" disabled selected>Seleccionar...</option>
            <option value="libre">Formación personalizada</option>
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

        <!-- Lista de Jugadores Convocados -->
        <h3 class="text-xl font-semibold text-gray-800 mt-4">Jugadores Convocados</h3>
        <div id="convocados-list" class="bg-gray-100 p-2 rounded-lg min-h-[150px] max-h-[40vh] overflow-y-auto">
            <div id="convocados-body" class="flex flex-wrap gap-2">
                <!-- Aquí se inyectarán los jugadores convocados -->
            </div>
        </div>

        <!-- Campo de Fútbol -->
        <div id="field-container" class="relative bg-green-500 h-96 w-4/5 mx-auto flex justify-center items-center mt-4">
            <img src="{{ asset('Imagenes/campo_futbol.jpg') }}" alt="Campo de Fútbol" class="w-full h-full object-cover">
            <div id="player-spots" class="absolute inset-0 flex justify-center items-center">
                <!-- Aquí se inyectarán las posiciones según la formación -->
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-center mt-4">
            <button id="edit-system-btn" class="hidden bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" onclick="enableEditMode()">
                Editar Formación
            </button>
            
            <button id="save-system-btn" class="hidden bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700" onclick="saveFormationChanges()">
                Guardar Formación
            </button>
        </div>

        <!-- Lista de Suplentes (Ahora sin scroll y con ajuste automático de filas) -->
        <h3 class="text-xl font-semibold text-gray-800 mt-4">Suplentes</h3>
        <div id="suplentes-list" class="bg-gray-200 border border-gray-300 p-3 rounded-lg shadow-md">
            <div id="suplentes-body" class="flex flex-wrap gap-3 justify-center">
                <!-- Aquí se inyectarán los suplentes -->
                 <br id="br-placeholder">
            </div>
        </div>



        <!-- Botones de acción (Siempre visibles) -->
        <div class="mt-4 flex justify-between bg-white p-4 shadow-md">
            <button onclick="saveAlineacion()" class="bg-green-500 text-white px-4 py-2 rounded-lg">Guardar Alineación</button>
            <button onclick="closeAlineador()" class="bg-red-500 text-white px-4 py-2 rounded-lg">Cerrar</button>
        </div>
    </div>
</div>


<!-- Contenedor oculto con datos en `data-attributes` -->
<div id="alineador-data" 
     data-players='@json($team->players)' 
     data-convocados='@json($convocados)'></div>





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
            <button type="button" onclick="toggleSelectAll()" class="bg-gray-500 text-white px-4 py-2 rounded-lg mb-2">
                Seleccionar Todos
            </button>
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
    //EDITAR JUGADOR
    function editPlayer(id) {
    document.getElementById('edit-btn-' + id).classList.add('hidden');
    document.getElementById('save-btn-' + id).classList.remove('hidden');
    document.getElementById('cancel-btn-' + id).classList.remove('hidden'); // Mostrar botón Cancelar
    document.getElementById('delete-form-' + id).classList.add('hidden'); // Ocultar botón Eliminar

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
}
//CANCELAR EDICION JUGADOR
function cancelEditPlayer(id) {
    document.getElementById('edit-btn-' + id).classList.remove('hidden');
    document.getElementById('save-btn-' + id).classList.add('hidden');
    document.getElementById('cancel-btn-' + id).classList.add('hidden'); // Ocultar botón Cancelar
    document.getElementById('delete-form-' + id).classList.remove('hidden'); // Mostrar botón Eliminar

    document.getElementById('pos-' + id).classList.remove('hidden');
    document.getElementById('edit-pos-' + id).classList.add('hidden');

    document.getElementById('perfil-' + id).classList.remove('hidden');
    document.getElementById('edit-perfil-' + id).classList.add('hidden');

    document.getElementById('min-' + id).classList.remove('hidden');
    document.getElementById('edit-min-' + id).classList.add('hidden');

    document.getElementById('goles-' + id).classList.remove('hidden');
    document.getElementById('edit-goles-' + id).classList.add('hidden');

    document.getElementById('asistencias-' + id).classList.remove('hidden');
    document.getElementById('edit-asistencias-' + id).classList.add('hidden');

    document.getElementById('titular-' + id).classList.remove('hidden');
    document.getElementById('edit-titular-' + id).classList.add('hidden');

    document.getElementById('suplente-' + id).classList.remove('hidden');
    document.getElementById('edit-suplente-' + id).classList.add('hidden');
}


    function savePlayer(id) {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let posicion = document.getElementById('edit-pos-' + id).value;
    let perfil = document.getElementById('edit-perfil-' + id).value;
    let minutos = document.getElementById('edit-min-' + id).value;
    let asistencias = document.getElementById('edit-asistencias-' + id).value;
    let titular = document.getElementById('edit-titular-' + id).value;
    let suplente = document.getElementById('edit-suplente-' + id).value;
    
    // Obtener goles o goles encajados según la posición
    let goles = document.getElementById('edit-goles-' + id).value;
    let form = document.createElement('form');
    form.method = 'POST';
    form.action = `/players/${id}`;

    form.appendChild(createHiddenInput('_token', csrfToken));
    form.appendChild(createHiddenInput('_method', 'PATCH'));
    form.appendChild(createHiddenInput('posicion', posicion));
    form.appendChild(createHiddenInput('perfil', perfil));
    form.appendChild(createHiddenInput('minutos_jugados', minutos));
    form.appendChild(createHiddenInput('asistencias', asistencias));
    form.appendChild(createHiddenInput('titular', titular));
    form.appendChild(createHiddenInput('suplente', suplente));

    // 📌 Si el jugador es portero, enviar "goles_encajados" en lugar de "goles"
    if (posicion === "Portero") {
        form.appendChild(createHiddenInput('goles_encajados', goles));
    } else {
        form.appendChild(createHiddenInput('goles', goles));
    }

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
    document.getElementById('cancel-btn-match-' + id).classList.remove('hidden');
    document.getElementById('delete-form-match-' + id).classList.add('hidden');

    let fields = ['fecha', 'goles-favor', 'goles-contra', 'resultado', 'actuacion'];

    fields.forEach(field => {
        document.getElementById(`${field}-${id}`).classList.add('hidden');
        document.getElementById(`edit-${field}-${id}`).classList.remove('hidden');
    });
}


function saveMatch(id) {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let data = {
        _token: csrfToken,
        _method: 'PATCH',
        fecha_partido: document.getElementById(`edit-fecha-${id}`).value,
        goles_a_favor: document.getElementById(`edit-goles-favor-${id}`).value,
        goles_en_contra: document.getElementById(`edit-goles-contra-${id}`).value,
        resultado: document.getElementById(`edit-resultado-${id}`).value,
        actuacion_equipo: document.getElementById(`edit-actuacion-${id}`).value
    };

    let form = document.createElement('form');
    form.method = 'POST';
    form.action = `/matches/${id}`;

    for (let key in data) {
        form.appendChild(createHiddenInput(key, data[key]));
    }

    document.body.appendChild(form);
    form.submit();
}

function cancelEditMatch(id) {
    document.getElementById('edit-btn-match-' + id).classList.remove('hidden');
    document.getElementById('save-btn-match-' + id).classList.add('hidden');
    document.getElementById('cancel-btn-match-' + id).classList.add('hidden');
    document.getElementById('delete-form-match-' + id).classList.remove('hidden');

    let fields = ['fecha', 'goles-favor', 'goles-contra', 'resultado', 'actuacion'];

    fields.forEach(field => {
        document.getElementById(`${field}-${id}`).classList.remove('hidden');
        document.getElementById(`edit-${field}-${id}`).classList.add('hidden');
    });
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
            alert('Convocatoria guardada correctamente');
            location.reload();  // 🔄 Recargar la página después de guardar
        } else {
            alert('Error al guardar la convocatoria.');
        }
    })
    .catch(error => console.error('Error en saveConvocatoria():', error));
}

function toggleSelectAll() {
    let checkboxes = document.querySelectorAll('input[name="convocados[]"]');
    let allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
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
let editMode = false; // 🔹 Estado de edición

function openAlineador(matchId) {
    let formationSelector = document.getElementById('formation-selector');
    let editButton = document.getElementById('edit-system-btn');
    let saveButton = document.getElementById('save-system-btn');

    // Restablecer el selector de formación a "Seleccionar..."
    formationSelector.selectedIndex = 0;

    let fieldContainer = document.getElementById('player-spots');
    fieldContainer.innerHTML = "";
    currentMatchId = matchId;
    document.getElementById('alineadorModal').classList.remove('hidden');

    // Ocultar botones al abrir el modal
    editButton.classList.add('hidden');
    saveButton.classList.add('hidden');

    // Asegurar que convocadosPorPartido[currentMatchId] tenga datos
    if (!convocadosPorPartido[currentMatchId]) {
        let rawConvocados = document.getElementById('alineador-data').dataset.convocados;
        try {
            convocadosPorPartido[currentMatchId] = JSON.parse(rawConvocados);
        } catch (error) {
            console.error("⚠ Error al parsear convocados desde el HTML:", error);
        }
    }

    console.log(`✅ Convocados cargados para el partido ${currentMatchId}:`, convocadosPorPartido[currentMatchId]);

    loadConvocados();

    fetch(`/matches/${currentMatchId}/get-alineacion`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.formacion) {
                let optionExists = Array.from(formationSelector.options).some(option => option.value === data.formacion);
                
                if (optionExists) {
                    formationSelector.value = data.formacion;
                    editButton.classList.remove('hidden');
                } else {
                    formationSelector.selectedIndex = 0;
                }

                updateFormation(data.formacion, data.alineacion);
            } else {
                formationSelector.selectedIndex = 0;
                console.error("No se pudo cargar la alineación guardada.");
            }
        })
        .catch(error => console.error("Error al obtener la alineación guardada:", error));
}



// 🔹 Activar modo edición (Mover libremente los círculos)
function enableEditMode() {
    editMode = true;
    document.getElementById('edit-system-btn').classList.add('hidden');
    document.getElementById('save-system-btn').classList.remove('hidden');

    document.querySelectorAll('.dropzone').forEach(positionDiv => {
        positionDiv.style.cursor = "grab";
        positionDiv.ondragstart = null; // Deshabilita el arrastre de jugadores

        positionDiv.onmousedown = function(event) {
            event.preventDefault();
            positionDiv.style.cursor = "grabbing";

            let initialX = event.clientX;
            let initialY = event.clientY;
            let startX = positionDiv.offsetLeft;
            let startY = positionDiv.offsetTop;

            function moveAt(event) {
                let newX = startX + (event.clientX - initialX);
                let newY = startY + (event.clientY - initialY);
                positionDiv.style.left = `${newX}px`;
                positionDiv.style.top = `${newY}px`;
            }

            function onMouseMove(event) {
                moveAt(event);
            }

            document.addEventListener('mousemove', onMouseMove);

            positionDiv.onmouseup = function() {
                document.removeEventListener('mousemove', onMouseMove);
                positionDiv.style.cursor = "grab";
                positionDiv.onmouseup = null;
            };
        };

        positionDiv.ondragstart = function() {
            return false;
        };
    });
}


// 🔹 Guardar posiciones y volver al modo normal
function saveFormationChanges() {
    editMode = false;
    document.getElementById('edit-system-btn').classList.remove('hidden');
    document.getElementById('save-system-btn').classList.add('hidden');

    document.querySelectorAll('.dropzone').forEach(positionDiv => {
        positionDiv.style.cursor = "pointer"; 
        positionDiv.onmousedown = null;
        enableDragDrop(positionDiv);
    });
}


// 🔹 Función para permitir arrastrar jugadores dentro del círculo
function enableDragDrop(positionDiv) {
    positionDiv.ondragover = function(event) {
        event.preventDefault();
    };

    positionDiv.ondrop = function(event) {
        event.preventDefault();
        let playerId = event.dataTransfer.getData("playerId");

        if (!playerId) return; 

        let existingPlayerId = positionDiv.getAttribute("data-player-id");

        if (existingPlayerId) {
            // Intercambiar jugadores si la posición está ocupada
            let sourceDiv = document.querySelector(`.dropzone[data-player-id='${playerId}']`);
            if (sourceDiv) {
                let tempPlayerId = existingPlayerId;
                sourceDiv.setAttribute("data-player-id", tempPlayerId);
                sourceDiv.textContent = allPlayers.find(p => p.id == tempPlayerId).dorsal;

                positionDiv.setAttribute("data-player-id", playerId);
                positionDiv.textContent = allPlayers.find(p => p.id == playerId).dorsal;
            }
        } else {
            // Mover jugador si la posición está vacía
            let player = allPlayers.find(p => p.id == playerId);
            if (player) {
                removeFromField(playerId);
                removeFromSuplentes(playerId);
                removeFromConvocados(playerId); // 🔹 Ahora también lo elimina de la lista de convocados

                positionDiv.textContent = player.dorsal;
                positionDiv.setAttribute("data-player-id", player.id);
                selectedPlayers[positionDiv.getAttribute("data-index")] = player.id;
            }
        }
    };

    positionDiv.ondragstart = function(event) {
        let playerId = positionDiv.getAttribute("data-player-id");
        if (playerId) {
            event.dataTransfer.setData("playerId", playerId);
        }
    };
}

// 🔹 Modificar updateFormation para soportar modo edición y jugadores dentro de círculos
function updateFormation(formation = null, alineacionGuardada = []) {
    let formationSelector = document.getElementById('formation-selector');
    let selectedFormation = formation || formationSelector.value;
    let fieldContainer = document.getElementById('player-spots');
    fieldContainer.innerHTML = "";

    let formations = {
        '1-4-4-2': [[10, 45], [30, 80], [30, 55], [30, 35], [30, 10], [50, 80], [50, 55], [50, 35], [50, 10], [70, 55], [70, 35]],
        '1-4-3-3': [[10, 45], [30, 80], [30, 55], [30, 35], [30, 10], [50, 20], [50, 45], [50, 70], [70, 20], [70, 45], [70, 70]],
        '1-5-3-2': [[10, 45], [30, 20], [30, 45], [30, 70], [40, 87], [40, 0], [50, 20], [50, 45], [50, 70], [70, 55], [70, 35]],
        'libre': [[10, 45], [20, 20], [20, 40], [20, 60], [20, 80], [30, 20], [30, 40], [30, 60], [30, 80], [40, 40], [40, 60]],
        '1-1-2-1': [[10, 45], [30, 45], [50,25 ], [50, 65], [70, 45]],
        '1-2-1-1': [[10, 45], [30, 25], [30, 65], [50, 45], [70, 45]],
        '1-3-2-1': [[10, 45], [30, 20], [30, 45], [30, 70], [50, 30], [50, 60], [70, 45]],
        '1-2-3-1': [[10, 45], [30, 30], [30, 60], [50, 20], [50, 45], [50, 70], [70, 45],],
        '1-2-4-1': [[10, 45], [30, 30], [30, 60], [50, 30], [50, 87], [50, 0], [50, 60], [70, 45],],
        '1-3-3-1': [[10, 45], [30, 20], [30, 45], [30, 70], [50, 20], [50, 45], [50, 70], [70, 45],]

    };

    if (!formations[selectedFormation]) return;

    formations[selectedFormation].forEach((pos, index) => {
        let positionDiv = document.createElement('div');
        positionDiv.className = "dropzone w-12 h-12 bg-white border border-gray-800 rounded-full flex items-center justify-center cursor-pointer";
        positionDiv.style.position = "absolute";
        positionDiv.style.top = `${pos[1]}%`;
        positionDiv.style.left = `${pos[0]}%`;
        positionDiv.setAttribute("data-index", index);
        positionDiv.setAttribute("draggable", true);

        let alineado = alineacionGuardada.find(a => a.posicion == index);
        if (alineado) {
            let player = allPlayers.find(p => p.id == alineado.player_id);
            if (player) {
                positionDiv.textContent = player.dorsal;
                positionDiv.setAttribute("data-player-id", player.id);
                selectedPlayers[index] = player.id;
            }
        }

        fieldContainer.appendChild(positionDiv);

        enableDragDrop(positionDiv); // 🔹 Activar la función de arrastrar jugadores  
    });
    enableSuplentesDrop();
}


function loadConvocados() {
    let convocadosBody = document.getElementById('convocados-body');
    convocadosBody.innerHTML = "";

    // Verificar si `currentMatchId` es válido
    if (!currentMatchId) {
        console.error("⚠ Error: currentMatchId no está definido.");
        return;
    }

    // Verificar si `convocadosPorPartido[currentMatchId]` tiene datos
    let convocados = convocadosPorPartido[currentMatchId];

    if (!convocados || convocados.length === 0) {
        console.warn(`⚠ No hay jugadores convocados para el partido ${currentMatchId}.`);
        return;
    }

    console.log(`✅ Cargando convocados para el partido ${currentMatchId}:`, convocados);

    convocados.forEach(playerId => {
        addToConvocados(playerId);
    });
}

function addToConvocados(playerId) {
    let convocadosBody = document.getElementById('convocados-body');
    let player = allPlayers.find(p => p.id == playerId);

    if (!player) {
        console.warn(`⚠ No se encontró el jugador con ID ${playerId} en la lista de jugadores.`);
        return;
    }

    let playerDiv = document.createElement('div');
    playerDiv.className = "draggable bg-blue-500 text-white px-3 py-1 rounded cursor-pointer w-full";
    playerDiv.innerHTML = `<span>${player.dorsal} - ${player.nombre} (${player.posicion})</span>`;
    playerDiv.setAttribute("draggable", true);
    playerDiv.setAttribute("data-player-id", player.id);

    playerDiv.ondragstart = function(event) {
        event.dataTransfer.setData("playerId", player.id);
    };

    convocadosBody.appendChild(playerDiv);
}


// 🔹 Permitir arrastrar del campo a suplentes
function enableSuplentesDrop() {
    let suplentesBody = document.getElementById('suplentes-body');

    suplentesBody.ondragover = function(event) {
        event.preventDefault();
    };

    suplentesBody.ondrop = function(event) {
        event.preventDefault();
        let playerId = event.dataTransfer.getData("playerId");

        if (playerId) {
            addToSuplentes(playerId);
            removeFromField(playerId);
            removeFromConvocados(playerId); // 🔹 También lo elimina de la lista de convocados
        }
    };
}

function addToSuplentes(playerId) {
    let suplentesBody = document.getElementById('suplentes-body');
    removePlaceholderBr();
    if (suplentesBody.querySelector(`[data-player-id='${playerId}']`)) return;

    let player = allPlayers.find(p => p.id == playerId);
    if (player) {
        let playerDiv = document.createElement('div');
        playerDiv.className = "draggable bg-gray-500 text-white px-3 py-1 rounded cursor-pointer w-full";
        playerDiv.innerHTML = `<span>${player.dorsal} - ${player.nombre} (${player.posicion})</span>`;
        playerDiv.setAttribute("draggable", true);
        playerDiv.setAttribute("data-player-id", player.id);

        playerDiv.ondragstart = function(event) {
            event.dataTransfer.setData("playerId", player.id);
        };

        suplentesBody.appendChild(playerDiv);
    }
}

function removeFromField(playerId) {
    let fieldPositions = document.querySelectorAll('.dropzone');

    fieldPositions.forEach(position => {
        if (position.getAttribute("data-player-id") === playerId) {
            position.textContent = "";
            position.removeAttribute("data-player-id");
            delete selectedPlayers[position.getAttribute("data-index")];
        }
    });
}

function removeFromSuplentes(playerId) {
    let playerDiv = document.querySelector(`#suplentes-body [data-player-id='${playerId}']`);
    if (playerDiv) {
        playerDiv.remove();
    }
}

function removeFromConvocados(playerId) {
    let playerDiv = document.querySelector(`#convocados-body [data-player-id='${playerId}']`);
    if (playerDiv) {
        playerDiv.remove();
    }
}

function saveAlineacion() {
    let alineacion = [];

    document.querySelectorAll(".dropzone").forEach(positionDiv => {
        let playerId = positionDiv.getAttribute("data-player-id");
        let index = positionDiv.getAttribute("data-index");
        if (playerId) {
            alineacion.push({
                posicion: index,
                player_id: playerId
            });
        }
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

function closeAlineador() {
    document.getElementById('alineadorModal').classList.add('hidden');
}

document.getElementById('formation-selector').addEventListener('change', function() {
    let formationSelector = document.getElementById('formation-selector');
    let editButton = document.getElementById('edit-system-btn');
    let saveButton = document.getElementById('save-system-btn');

    // Solo mostrar el botón si el usuario ha seleccionado una formación válida
    if (formationSelector.value && formationSelector.value !== "") {
        editButton.classList.remove('hidden');
        saveButton.classList.add('hidden');
    } else {
        editButton.classList.add('hidden');
        saveButton.classList.add('hidden');
    }
});

function removePlaceholderBr() {
        let placeholder = document.getElementById("br-placeholder");
        if (placeholder) {
            placeholder.remove();
        }
    }

    // Detectar cuando un jugador es soltado en la lista de suplentes
    document.getElementById("suplentes-body").addEventListener("drop", function(event) {
        removePlaceholderBr();
    });

</script>

@endsection
   