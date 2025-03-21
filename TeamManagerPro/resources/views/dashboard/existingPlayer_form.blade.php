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
                <button type="button" onclick="closeModal('existingPlayerModal')" class="bg-gray-500 text-white px-4 py-2 rounded-lg">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
