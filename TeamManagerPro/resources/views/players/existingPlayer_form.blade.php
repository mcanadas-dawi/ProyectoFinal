<!-- Modal para Añadir Jugadores de Otras Plantillas -->
<div id="existingPlayerModal" class="hidden fixed inset-0 bg-[#1E293B]/80 flex justify-center items-center z-50 p-3 sm:p-0">
    <div class="bg-[#1E3A8A] rounded-lg p-4 sm:p-6 w-full sm:w-4/5 md:w-2/3 lg:w-1/2 xl:w-1/3 shadow-lg text-white font-sans max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg sm:text-xl font-title text-[#FACC15] mb-3 sm:mb-4 text-center uppercase">Añadir Jugadores Existentes</h2>

        <form action="{{ route('players.addToTeam') }}" method="POST">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">

            <!-- Lista con checkboxes -->
            <div class="max-h-[50vh] sm:max-h-60 overflow-y-auto mb-3 sm:mb-4">
                <button type="button" onclick="toggleSelectAllPlayers()" class="bg-[#FACC15] text-black px-3 sm:px-4 py-1 sm:py-2 rounded-lg mb-2 sm:mb-3 hover:brightness-110 text-sm sm:text-base w-full sm:w-auto">
                    Seleccionar Todos
                </button>
                @foreach ($allPlayers->filter(function ($player) use ($team) {
                    return $player->teams->contains('user_id', Auth::id()) && !$player->teams->contains('id', $team->id);
                }) as $player)
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="add-player-{{ $player->id }}" name="player_ids[]"
                            value="{{ $player->id }}"
                            class="mr-2 accent-[#00B140] scale-110">
                        <label for="add-player-{{ $player->id }}" class="text-white text-sm sm:text-base">
                            {{ $player->nombre }} {{ $player->apellido }} (DNI: {{ $player->dni }})
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="mt-3 sm:mt-4 flex flex-col sm:flex-row gap-2 sm:gap-0 justify-between">
                <button type="submit" class="bg-[#00B140] text-white px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 w-full sm:mr-2 text-sm sm:text-base">
                    Añadir Jugadores
                </button>
                <button type="button" onclick="closeModal('existingPlayerModal')" class="bg-[#EF4444] text-white px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 w-full sm:ml-2 text-sm sm:text-base">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSelectAllPlayers() {
    let checkboxes = document.querySelectorAll('input[name="player_ids[]"]');
    let allSelected = Array.from(checkboxes).every(checkbox => checkbox.checked);

    checkboxes.forEach(checkbox => {
        checkbox.checked = !allSelected;
    });
}

</script>
