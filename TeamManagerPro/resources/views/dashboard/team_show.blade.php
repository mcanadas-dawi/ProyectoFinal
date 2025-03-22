@extends('layouts.dashboard')

@section('content')
@if(session('success'))
    <div class="bg-green-500 text-white p-3 rounded mb-4 text-center">
        {{ session('success') }}
    </div>
@endif
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

<!-- Sección de Estadísticas -->
<div class="bg-teal-400 shadow-lg rounded-lg p-6 mb-6">
    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Estadísticas de la plantilla</h2>

    <table class="w-full text-center border-collapse bg-white rounded-lg">
        <thead class="bg-teal-500 text-gray-900">
            <tr class="border-b">
                <th class="p-2">Victorias</th>
                <th class="p-2">Empates</th>
                <th class="p-2">Derrotas</th>
                <th class="p-2">Puntos</th>
                <th class="p-2">Goles a Favor</th>
                <th class="p-2">Goles en Contra</th>
                <th class="p-2">Tarjetas Amarillas</th>
                <th class="p-2">Tarjetas Rojas</th>
                <th class="p-2">Valoración Media</th>
            </tr>
        </thead>
        <tbody class="text-gray-800">
        <td class="p-2">{{ $stats['victorias'] ?? 0 }}</td>
        <td class="p-2">{{ $stats['empates'] ?? 0 }}</td>
        <td class="p-2">{{ $stats['derrotas'] ?? 0 }}</td>
        <td class="p-2 font-bold">{{ $stats['puntos'] ?? 0 }}</td>
        <td class="p-2">{{ $stats['goles_favor'] ?? 0 }}</td>
        <td class="p-2">{{ $stats['goles_contra'] ?? 0 }}</td>
        <td class="p-2 text-yellow-600 font-bold">{{ $stats['tarjetas_amarillas'] ?? 0 }}</td>
        <td class="p-2 text-red-600 font-bold">{{ $stats['tarjetas_rojas'] ?? 0 }}</td>
        <td class="p-2 text-green-600 font-bold">{{ number_format($stats['valoracion_media'] ?? 0, 2) }}</td>

            </tr>
        </tbody>
    </table>
</div>

<!-- 📌 Formulario para añadir competición -->
<div id="competicion-form" class="hidden bg-white p-4 rounded shadow-lg">
    <form action="{{ route('rivales_liga.store') }}" method="POST">
        @csrf
        <label class="block">Equipo Rival:</label>
        <input type="text" name="nombre_equipo" class="w-full border p-2 rounded mb-2" required>

        <label class="block">Jornada:</label>
        <input type="number" name="jornada" class="w-full border p-2 rounded mb-2" required>

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Guardar</button>
        <button type="button" onclick="toggleForm('competicion-form')" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
    </form>
</div>

    <!-- Sección de Jugadores -->
    @if(session('success_player'))
        <div class="bg-green-500 text-white p-3 rounded mb-4 text-center">
            {{ session('success_player') }}
        </div>
    @endif
<div class="bg-blue-200 shadow-lg rounded-lg p-6 mb-6">
    <div class="flex items-center justify-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-900 flex-grow text-left">Jugadores</h2>
        <button onclick="openModal('addPlayerModal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Añadir Nuevo Jugador
        </button>
        @include('dashboard.player_form')

        <button onclick="openModal('existingPlayerModal')" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 ml-4">
            Añadir Jugador de Otra Plantilla
        </button>
        @include('dashboard.existingPlayer_form')
    </div>

    <table class="w-full text-center border-collapse">
        <thead class="bg-blue-300 text-gray-900">
            <tr class="border-b">
                <th class="p-2">Nombre</th>
                <th class="p-2">Apellido</th>
                <th class="p-2">Dorsal</th>
                <th class="p-2">Edad</th>
                <th class="p-2">Posición</th>
                <th class="p-2">Pie</th>
                <th class="p-2">Minutos</th>
                <th class="p-2">Goles/Encajados(POR)</th>
                <th class="p-2">Asist</th>
                <th class="p-2">Tit</th>
                <th class="p-2">Supl</th>
                <th class="p-2">Valoración</th>
                <th class="p-2">Amarillas</th>
                <th class="p-2">Rojas</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($team->players as $player)
                @php
                    $stats = $player->teamStats($team->id);
                @endphp
                <tr class="border-b bg-blue-100" id="player-row-{{ $player->id }}">
                    <td class="p-2">{{ $player->nombre }}</td>
                    <td class="p-2">{{ $player->apellido }}</td>
                    <td class="p-2">{{ $player->dorsal }}</td>
                    <td class="p-2">
                        <span id="edad-{{ $player->id }}"></span> 
                        <span class="hidden" id="fecha-nacimiento-{{ $player->id }}">{{ $player->fecha_nacimiento }}</span>
                    </td>
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
                        <span id="perfil-{{ $player->id }}">
                            @if($player->perfil == 'Diestro') D @else I @endif
                        </span>
                        <select name="perfil" class="hidden w-full p-1 border rounded" id="edit-perfil-{{ $player->id }}">
                            <option value="Diestro" @selected($player->perfil == 'Diestro')>Diestro</option>
                            <option value="Zurdo" @selected($player->perfil == 'Zurdo')>Zurdo</option>
                        </select>
                    </td>
                    <td class="p-2">{{ $stats->minutos_jugados ?? 0 }}</td>
                    <td class="p-2">{{ $stats->goles ?? 0 }}</td>
                    <td class="p-2">{{ $stats->asistencias ?? 0 }}</td>
                    <td class="p-2">{{ $stats->titular ?? 0 }}</td>
                    <td class="p-2">{{ $stats->suplente ?? 0 }}</td>
                    <td class="p-2 font-bold text-blue-600">{{ number_format($stats->valoracion ?? 0, 2) }}</td>
                    <td class="p-2 text-yellow-600 font-bold">{{ $stats->tarjetas_amarillas ?? 0 }}</td>
                    <td class="p-2 text-red-600 font-bold">{{ $stats->tarjetas_rojas ?? 0 }}</td>
                    <td class="p-2 text-center">
                        <!-- Botón Editar -->
                        <button onclick="editPlayer('{{ $player->id }}')" id="edit-btn-{{ $player->id }}" class="bg-yellow-500 text-white px-3 py-1 rounded">Editar</button>

                        <!-- Botón Guardar (oculto inicialmente) -->
                        <button onclick="savePlayer('{{ $player->id }}')" id="save-btn-{{ $player->id }}" class="hidden bg-green-500 text-white px-3 py-1 rounded">Guardar</button>

                        <!-- Botón Cancelar (oculto inicialmente) -->
                        <button onclick="cancelEditPlayer('{{ $player->id }}')" id="cancel-btn-{{ $player->id }}" class="hidden bg-gray-500 text-white px-3 py-1 rounded">Cancelar</button>

                        <!-- Botón Eliminar -->
                        <form action="{{ route('players.destroy', $player->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este jugador de la plantilla? Esta acción no se puede deshacer.')" id="delete-form-{{ $player->id }}" class="inline">
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


<!-- Sección de Partidos Amistosos -->
@if(session('success_amistoso'))
    <div class="bg-green-500 text-white p-3 rounded mb-4 text-center">
        {{ session('success_amistoso') }}
    </div>
@endif
<div class="bg-green-200 shadow-lg rounded-lg p-6 mb-6">
    <div class="flex items-center justify-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-900 flex-grow text-left">Partidos Amistosos</h2>
        <button onclick="openModal('amistosoModal')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Añadir Partido Amistoso
        </button>
    </div>

    <table class="w-full text-center border-collapse bg-white rounded-lg">
    <thead class="bg-green-300 text-gray-900">
    <tr class="border-b">
        <th class="p-2">Fecha</th>
        <th class="p-2">Equipo Rival</th>
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
@if(isset($partidosAmistosos) && count($partidosAmistosos) > 0)
    @foreach ($partidosAmistosos as $match)
    @include('matches.editFriendlyMatch_form')
        @php
            // Determinar el color de la fila según el resultado
            $colorClase = match ($match->resultado) {
                'Victoria' => 'bg-green-700', // Verde para victoria
                'Empate' => 'bg-yellow-500', // Amarillo para empate
                'Derrota' => 'bg-red-300', // Rojo para derrota
                default => 'bg-green-100', // Color neutro si no hay resultado
            };
        @endphp
        
        <tr id="match-row-{{ $match->id }}" class="border-b {{ $colorClase }}">
        <td class="p-2 text-center">
                <span id="fecha-{{ $match->id }}">{{ $match->fecha_partido }}</span>
                <input type="date" name="fecha_partido" class="hidden w-16 p-1 border rounded" id="edit-fecha-{{ $match->id }}" value="{{ $match->fecha_partido }}">
            </td>
            <td class="p-2 text-center">{{ $match->equipo_rival }}</td>
            

        <!-- Goles a Favor -->
        <td class="p-2 text-center">
            <span id="goles-favor-{{ $match->id }}">{{ $match->goles_a_favor }}</span>
            <input type="number" name="goles_a_favor" class="hidden w-16 p-1 border rounded" 
                id="edit-goles-favor-{{ $match->id }}" 
                value="{{ $match->goles_a_favor }}"
                onchange="updateResultado('{{ $match->id }}')">
        </td>

        <!-- Goles en Contra -->
        <td class="p-2 text-center">
            <span id="goles-contra-{{ $match->id }}">{{ $match->goles_en_contra }}</span>
            <input type="number" name="goles_en_contra" class="hidden w-16 p-1 border rounded" 
                id="edit-goles-contra-{{ $match->id }}" 
                value="{{ $match->goles_en_contra }}"
                onchange="updateResultado('{{ $match->id }}')">
        </td>

        <!-- Resultado (Solo Mostrar) -->
        <td class="p-2 text-center">
            <span id="resultado-{{ $match->id }}">{{ $match->resultado }}</span>
            <input type="hidden" name="resultado" id="edit-resultado-{{ $match->id }}" value="{{ $match->resultado }}">
        </td>




            <!-- Actuación del Equipo -->
            <td class="p-2 text-center">
                <span id="actuacion-{{ $match->id }}">{{ $match->actuacion_equipo !== null ? number_format($match->actuacion_equipo, 2) : 'N/A' }}</span>
                <input type="number" name="actuacion_equipo" step="0.01" min="0" max="10" class="hidden w-16 p-1 border rounded" id="edit-actuacion-{{ $match->id }}" value="{{ $match->actuacion_equipo }}">
            </td>

            <td class="p-2 text-center">
                <button onclick="openModal('convocatoriaModal')" class="bg-blue-500 text-white px-3 py-1 rounded">
                    Convocatoria
                </button>
            </td>
            <td class="p-2 text-center">
                <button onclick="openModal('alineadorModal')" class="bg-indigo-500 text-white px-3 py-1 rounded">
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
                <!-- Incluir el formulario desde el archivo parcial -->
                @include('matches.editFriendlyMatch_form', ['match' => $match])
            </td>
        </tr>
    @endforeach
    @else
    <tr>
        <td colspan="10" class="text-center text-gray-600 p-2">No hay partidos amistosos registrados.</td>
    </tr>
    @endif
</tbody>
</table>
</div>

<!-- 📌 Tabla de Partidos Oficiales (Liga) -->
@if(session('success_liga'))
    <div class="bg-green-500 text-white p-3 rounded mb-4 text-center">
        {{ session('success_liga') }}
    </div>
@endif
<div class="bg-blue-400 shadow-lg rounded-lg p-6 mb-6">
    <div class="flex items-center justify-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-900 flex-grow text-left">Partidos de Liga</h2>
        <!-- 📌 Botón para añadir partido amistoso -->
        <button onclick="openModal('ligaModal')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Añadir Partido de Liga
        </button>
        @include('dashboard.leagueMatch_form')
        </div>
        <table class="w-full text-center border-collapse bg-white rounded-lg">
        <thead class="bg-blue-500 text-gray-900">
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
        <tbody>
    @if(isset($partidosLiga) && count($partidosLiga) > 0)
        @foreach ($partidosLiga as $match)
            @php
                $colorClase = match ($match->resultado ?? '') {
                    'Victoria' => 'bg-green-700',
                    'Empate' => 'bg-yellow-500',
                    'Derrota' => 'bg-red-300',
                    default => 'bg-green-100',
                };
            @endphp
            <tr class="border-b {{ $colorClase }}">
                <td class="p-2">{{ $match->rivalLiga->jornada ?? 'N/A' }}</td>
                <td class="p-2">{{ $match->rivalLiga->nombre_equipo ?? 'N/A' }}</td>
                <td class="p-2">{{ $match->fecha_partido ?? 'Sin fecha' }}</td>
                <td class="p-2">{{ $match->goles_a_favor ?? 0 }}</td>
                <td class="p-2">{{ $match->goles_en_contra ?? 0 }}</td>
                <td class="p-2">{{ $match->resultado ?? 'Pendiente' }}</td>
                <td class="p-2">{{ $match->actuacion_equipo ?? 'N/A' }}</td>
                <td class="p-2"><button class="bg-indigo-500 text-white px-3 py-1 rounded">Alineador</button></td>
                <td class="p-2"><button class="bg-blue-500 text-white px-3 py-1 rounded">Convocatoria</button></td>
                <td class="p-2">
                    <button class="bg-yellow-500 text-white px-3 py-1 rounded">Editar</button>
                    <button class="bg-red-500 text-white px-3 py-1 rounded">Eliminar</button>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="10" class="text-center text-gray-600 p-2">No hay partidos de liga registrados.</td>
        </tr>
    @endif
</tbody>
</div>



<!-- 📌 Modal para añadir partido de liga -->
<div id="ligaModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Añadir Partido de Liga</h2>
        <form action="{{ route('matches.store') }}" method="POST">
            @csrf
            <input type="hidden" name="tipo" value="liga">

            <label class="block text-gray-700">Seleccionar Rival:</label>
            <select name="rival_liga_id" class="w-full border p-2 rounded mb-2" required>
                @if(isset($rivales) && count($rivales) > 0)
                    @foreach ($rivales as $rival)
                        <option value="{{ $rival->id }}">{{ $rival->nombre_equipo }} - Jornada {{ $rival->jornada }}</option>
                    @endforeach
                @else
                    <option value="" disabled>No hay rivales disponibles</option>
                @endif
            </select>


            <label class="block text-gray-700">Fecha:</label>
            <input type="date" name="fecha_partido" class="w-full border p-2 rounded mb-2" required>

            <div class="flex justify-between mt-4">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Guardar</button>
                <button type="button" onclick="closeModal('ligaModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            </div>
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
            <button onclick="closeModal('alineadorModal')" class="bg-red-500 text-white px-4 py-2 rounded-lg">Cerrar</button>
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
                <button type="button" onclick="toggleSelectAll()" class="bg-gray-500 text-white px-4 py-2 rounded-lg mb-2">
                    Seleccionar Todos
                </button>
                @foreach ($team->players as $player)
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="player-{{ $player->id }}" name="convocados[]"
                            value="{{ $player->id }}" class="mr-2"
                            {{ isset($convocados) && in_array($player->id, $convocados[$match->id] ?? []) ? 'checked' : '' }}>
                        <label for="player-{{ $player->id }}">{{ $player->nombre }} {{ $player->apellido }}</label>
                    </div>
                @endforeach
            </div>


            <div class="mt-4 flex justify-between">
                <button type="button" onclick="saveConvocatoria()" class="bg-green-500 text-white px-4 py-2 rounded-lg">
                    Guardar Convocatoria
                </button>
                <button type="button" onclick="closeModal('convocatoriaModal')" class="bg-gray-500 text-white px-4 py-2 rounded-lg">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// 📌 Abrir y cerrar cualquier modal de manera dinámica
function openModal(modalId) {
    let modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
    } else {
        console.error(`❌ Error: No se encontró el modal con id "${modalId}"`);
    }
}

function closeModal(modalId) {
    let modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    } else {
        console.error(`❌ Error: No se encontró el modal con id "${modalId}"`);
    }
}

// 📌 Alternar selección de todos los jugadores en la convocatoria
function toggleSelectAll() {
    let checkboxes = document.querySelectorAll('input[name="convocados[]"]');
    let allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

    checkboxes.forEach(checkbox => checkbox.checked = !allChecked);
}

// 📌 Guardar convocatoria mediante AJAX
function saveConvocatoria() {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let matchId = document.querySelector('input[name="match_id"]').value;
    
    let seleccionados = Array.from(document.querySelectorAll('input[name="convocados[]"]:checked'))
                             .map(checkbox => checkbox.value);

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
            location.reload();
        } else {
            alert('Error al guardar la convocatoria.');
        }
    })
    .catch(error => console.error('❌ Error en saveConvocatoria():', error));
}

// 📌 Editar, cancelar y guardar jugadores
function editPlayer(id) {
    toggleEditState(id, true);
}

function cancelEditPlayer(id) {
    toggleEditState(id, false);
}

function savePlayer(id) {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let data = {
        _token: csrfToken,
        _method: 'PATCH',
        posicion: document.getElementById(`edit-pos-${id}`).value,
        perfil: document.getElementById(`edit-perfil-${id}`).value
    };

    fetch(`/players/${id}`, {
        method: 'POST',  // Usamos POST porque el método PATCH se envía con _method
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar la página para mostrar el mensaje flash
            location.reload();
        } else {
            alert('Error al actualizar el jugador');
        }
    })
    .catch(error => {
        console.error('Error al guardar el jugador:', error);
        alert('Ocurrió un error al guardar el jugador.');
    });
}





// 📌 Editar, cancelar y guardar partidos
function editMatch(id) {
    toggleMatchEditState(id, true);
}

function cancelEditMatch(id) {
    toggleMatchEditState(id, false);
}

function saveMatch(id) {
    const form = document.getElementById(`edit-match-form-${id}`);
    const csrfToken = form.querySelector('input[name="_token"]').value;

    // Obtener los valores directamente desde los campos editados
    const golesFavor = document.getElementById(`edit-goles-favor-${id}`).value;
    const golesContra = document.getElementById(`edit-goles-contra-${id}`).value;
    const resultado = document.getElementById(`edit-resultado-${id}`).value;
    const actuacion = document.getElementById(`edit-actuacion-${id}`).value;
    const fechaPartido = document.getElementById(`edit-fecha-${id}`).value;
    const equipoRival = document.getElementById(`edit-equipo-rival-${id}`).value;
    const tipo = "amistoso";

    // Actualizar los campos ocultos del formulario
    form.querySelector(`input[name="goles_a_favor"]`).value = golesFavor;
    form.querySelector(`input[name="goles_en_contra"]`).value = golesContra;
    form.querySelector(`input[name="resultado"]`).value = resultado;
    form.querySelector(`input[name="actuacion_equipo"]`).value = actuacion;
    form.querySelector(`input[name="fecha_partido"]`).value = fechaPartido;
    form.querySelector(`input[name="equipo_rival"]`).value = equipoRival;
    form.querySelector(`input[name="tipo"]`).value = tipo;

    // Enviar el formulario al backend usando PATCH
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: new FormData(form)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                alert('Error en la actualización: ' + (errorData.message || 'Ocurrió un error en el servidor.'));
                throw new Error("Error en la actualización del partido");
            });
        }
        return response.json();
    })
    .then(data => {
        toggleMatchEditState(id, false);
        location.reload();
    })
    .catch(error => {
        alert('Ocurrió un error al guardar el partido: ' + error.message);
    });
}



// 📌 Función reutilizable para alternar estados de edición
function toggleEditState(id, editing) {
    let action = editing ? 'add' : 'remove';

    // Botones de edición y guardado
    document.getElementById(`edit-btn-${id}`).classList[action]('hidden');
    document.getElementById(`save-btn-${id}`).classList[action === 'add' ? 'remove' : 'add']('hidden');
    document.getElementById(`cancel-btn-${id}`).classList[action === 'add' ? 'remove' : 'add']('hidden');
    document.getElementById(`delete-form-${id}`).classList[action === 'add' ? 'add' : 'remove']('hidden');

    // Actualizar los campos de posición y perfil
    let fields = ['pos', 'perfil'];
    fields.forEach(field => {
        const spanElement = document.getElementById(`${field}-${id}`);
        const selectElement = document.getElementById(`edit-${field}-${id}`);

        if (editing) {
            spanElement.classList.add('hidden');
            selectElement.classList.remove('hidden');
        } else {
            spanElement.classList.remove('hidden');
            selectElement.classList.add('hidden');
        }
    });
}




// 📌 Función reutilizable para alternar edición de partidos
function toggleMatchEditState(id, editing) {
    let action = editing ? 'add' : 'remove';

    document.getElementById(`edit-btn-match-${id}`).classList[action]('hidden');
    document.getElementById(`save-btn-match-${id}`).classList[action === 'add' ? 'remove' : 'add']('hidden');
    document.getElementById(`cancel-btn-match-${id}`).classList[action === 'add' ? 'remove' : 'add']('hidden');
    document.getElementById(`delete-form-match-${id}`).classList[action === 'add' ? 'add' : 'remove']('hidden');

    let fields = ['fecha', 'goles-favor', 'goles-contra', 'resultado', 'actuacion'];
    fields.forEach(field => {
        document.getElementById(`${field}-${id}`).classList[action]('hidden');
        document.getElementById(`edit-${field}-${id}`).classList[action === 'add' ? 'remove' : 'add']('hidden');
    });
}


// 📌 Función reutilizable para enviar formularios
function submitForm(action, data) {
    let form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    for (let key in data) {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = data[key];
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
}

// 📌 Actualizar resultado de partido en base a los goles
function updateResultado(matchId) {
    // Obtener los valores de los campos de goles
    const golesFavor = parseInt(document.getElementById(`edit-goles-favor-${matchId}`).value) || 0;
    const golesContra = parseInt(document.getElementById(`edit-goles-contra-${matchId}`).value) || 0;

    // Calcular el resultado según la lógica proporcionada
    let resultado = 'Derrota';
    if (golesFavor > golesContra) {
        resultado = 'Victoria';
    } else if (golesFavor === golesContra) {
        resultado = 'Empate';
    }

    // Actualizar el resultado en la interfaz
    document.getElementById(`resultado-${matchId}`).innerText = resultado;
    document.getElementById(`edit-resultado-${matchId}`).value = resultado;

    // Actualizar el color de la fila
    actualizarColorFila(matchId);

    // Mostrar los valores actualizados en los campos
    document.getElementById(`goles-favor-${matchId}`).innerText = golesFavor;
    document.getElementById(`goles-contra-${matchId}`).innerText = golesContra;
}

// 📌 Cambiar el color de la fila según el resultado
function actualizarColorFila(id) {
    const resultado = document.getElementById(`edit-resultado-${id}`).value;
    const fila = document.getElementById(`match-row-${id}`);

    // Limpiar clases de color anteriores
    fila.classList.remove("bg-green-700", "bg-yellow-500", "bg-red-300");

    // Asignar el color adecuado según el resultado
    if (resultado === "Victoria") {
        fila.classList.add("bg-green-700");
    } else if (resultado === "Empate") {
        fila.classList.add("bg-yellow-500");
    } else if (resultado === "Derrota") {
        fila.classList.add("bg-red-300");
    }
}
function calcularEdad(fechaNacimiento) {
        const hoy = new Date();
        const nacimiento = new Date(fechaNacimiento);

        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        const mes = hoy.getMonth() - nacimiento.getMonth();

        // Ajuste si el mes o el día aún no han pasado este año
        if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }

        return edad;
    }

    // Actualizar todas las edades en la tabla de jugadores
    function actualizarEdades() {
        const elementos = document.querySelectorAll('[id^="fecha-nacimiento-"]');

        elementos.forEach(elemento => {
            const id = elemento.id.replace("fecha-nacimiento-", "edad-");
            const fechaNacimiento = elemento.textContent.trim();
            const edad = calcularEdad(fechaNacimiento);
            const edadElemento = document.getElementById(id);

            if (edadElemento) {
                edadElemento.innerText = edad;
            }
        });
    }
    window.onload = actualizarEdades;

</script>
@endsection