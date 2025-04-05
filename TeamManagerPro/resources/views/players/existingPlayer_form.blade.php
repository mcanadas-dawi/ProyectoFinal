<!-- Modal para Añadir Jugadores de Otras Plantillas -->
<div id="existingPlayerModal" class="hidden fixed inset-0 bg-[#1E293B]/80 flex justify-center items-center z-50">
    <div class="bg-[#1E3A8A] rounded-lg p-6 w-1/3 shadow-lg text-white font-sans">
        <h2 class="text-2xl font-title text-[#FACC15] mb-4 text-center uppercase">Añadir Jugador Existente</h2>
        
        <form action="{{ route('players.addToTeam') }}" method="POST">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">

            <div class="mb-4">
                <label for="player_id" class="block text-[#FACC15] font-semibold mb-1">Seleccionar jugador:</label>
                <select name="player_id" id="player_id" class="w-full p-2 border rounded bg-white text-black text-center" required>
                    <option value="">Seleccionar...</option>
                    @foreach ($allPlayers as $player)
                        <option value="{{ $player->id }}">{{ $player->nombre }} {{ $player->apellido }} (DNI: {{ $player->dni }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-between">
                <button type="submit" class="bg-[#00B140] text-white px-4 py-2 rounded-lg hover:brightness-110 w-full mr-2">
                    Añadir Jugador
                </button>
                <button type="button" onclick="closeModal('existingPlayerModal')" class="bg-[#EF4444] text-white px-4 py-2 rounded-lg hover:brightness-110 w-full ml-2">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
