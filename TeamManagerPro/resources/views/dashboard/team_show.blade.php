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

    <!-- Sección de Jugadores -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Jugadores</h2>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="p-2">Nombre</th>
                    <th class="p-2">Apellido</th>
                    <th class="p-2">Dorsal</th>
                    <th class="p-2">Posición</th>
                    <th class="p-2">Perfil</th>
                    <th class="p-2">Minutos Jugados</th>
                    <th class="p-2">Goles</th>
                    <th class="p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($team->players as $player)
                    <tr class="border-b" id="player-row-{{ $player->id }}">
                        <td class="p-2">{{ $player->nombre }}</td>
                        <td class="p-2">{{ $player->apellido }}</td>
                        <td class="p-2">{{ $player->dorsal }}</td>
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
                                <option value="Ambidiestro">Ambidiestro</option>
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
</div>

        <!-- Sección de Partidos -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Partidos</h2>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="p-2">Jornada</th>
                    <th class="p-2">Equipo Rival</th>
                    <th class="p-2">Fecha</th>
                    <th class="p-2">Goles a Favor</th>
                    <th class="p-2">Goles en Contra</th>
                    <th class="p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($team->matches as $match)
                    <tr class="border-b" id="match-row-{{ $match->id }}">
                        <td class="p-2">{{ $match->numero_jornada }}</td>
                        <td class="p-2">{{ $match->equipo_rival }}</td>
                        <td class="p-2">{{ $match->fecha_partido }}</td>
                        <td class="p-2">
                            <span id="goles-favor-{{ $match->id }}">{{ $match->goles_a_favor }}</span>
                            <input type="number" name="goles_a_favor" class="hidden w-16 p-1 border rounded" id="edit-goles-favor-{{ $match->id }}">
                        </td>
                        <td class="p-2">
                            <span id="goles-contra-{{ $match->id }}">{{ $match->goles_en_contra }}</span>
                            <input type="number" name="goles_en_contra" class="hidden w-16 p-1 border rounded" id="edit-goles-contra-{{ $match->id }}">
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
</div>

    <!-- Alineador táctico -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Alineación</h2>
        <select id="formation-selector" class="w-full p-2 border rounded-lg mb-2">
            @if ($team->modalidad == 'F5')
                <option value="1-1-2-1">1-1-2-1</option>
                <option value="libre">Libre</option>
            @elseif ($team->modalidad == 'F7')
                <option value="1-3-2-1">1-3-2-1</option>
                <option value="1-2-3-1">1-2-3-1</option>
                <option value="libre">Libre</option>
            @elseif ($team->modalidad == 'F8')
                <option value="1-2-4-1">1-2-4-1</option>
                <option value="1-3-3-1">1-3-3-1</option>
                <option value="1-2-3-1-1">1-2-3-1-1</option>
                <option value="1-2-3-2">1-2-3-2</option>
                <option value="libre">Libre</option>
            @elseif ($team->modalidad == 'F11')
                <option value="1-5-3-2">1-5-3-2</option>
                <option value="1-5-3-1-1">1-5-3-1-1</option>
                <option value="1-5-4-1">1-5-4-1</option>
                <option value="1-4-4-2">1-4-4-2</option>
                <option value="1-4-4-1-1">1-4-4-1-1</option>
                <option value="1-4-3-3">1-4-3-3</option>
                <option value="1-4-1-2-3">1-4-1-2-3</option>
                <option value="1-4-2-1-3">1-4-2-1-3</option>
                <option value="libre">Libre</option>
            @endif
        </select>
        <div id="field-container" class="bg-green-500 h-96 flex justify-center items-center">
            <img src="{{ asset('Imagenes/campo_futbol.jpg') }}" alt="Campo de Fútbol" class="w-full h-full object-cover">
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
    }

    function savePlayer(id) {
        document.getElementById('edit-btn-' + id).classList.remove('hidden');
        document.getElementById('save-btn-' + id).classList.add('hidden');
        
        document.getElementById('pos-' + id).classList.remove('hidden');
        document.getElementById('edit-pos-' + id).classList.add('hidden');
        
        document.getElementById('perfil-' + id).classList.remove('hidden');
        document.getElementById('edit-perfil-' + id).classList.add('hidden');
        
        document.getElementById('min-' + id).classList.remove('hidden');
        document.getElementById('edit-min-' + id).classList.add('hidden');
        
        document.getElementById('goles-' + id).classList.remove('hidden');
        document.getElementById('edit-goles-' + id).classList.add('hidden');
    }

    function editMatch(id) {
        document.getElementById('edit-btn-match-' + id).classList.add('hidden');
        document.getElementById('save-btn-match-' + id).classList.remove('hidden');

        document.getElementById('goles-favor-' + id).classList.add('hidden');
        document.getElementById('edit-goles-favor-' + id).classList.remove('hidden');

        document.getElementById('goles-contra-' + id).classList.add('hidden');
        document.getElementById('edit-goles-contra-' + id).classList.remove('hidden');
    }

    function saveMatch(id) {
        document.getElementById('edit-btn-match-' + id).classList.remove('hidden');
        document.getElementById('save-btn-match-' + id).classList.add('hidden');

        document.getElementById('goles-favor-' + id).innerText = document.getElementById('edit-goles-favor-' + id).value;
        document.getElementById('goles-contra-' + id).innerText = document.getElementById('edit-goles-contra-' + id).value;

        document.getElementById('goles-favor-' + id).classList.remove('hidden');
        document.getElementById('edit-goles-favor-' + id).classList.add('hidden');

        document.getElementById('goles-contra-' + id).classList.remove('hidden');
        document.getElementById('edit-goles-contra-' + id).classList.add('hidden');
    }
</script>



@endsection