<!-- Modal para Convocatoria -->
<div id="convocatoriaModal" class="hidden fixed inset-0 bg-[#1E293B]/80 flex justify-center items-center z-50 p-3 sm:p-0">
    <div class="bg-[#1E3A8A] rounded-lg p-4 sm:p-6 w-full sm:w-4/5 md:w-2/3 lg:w-1/2 xl:w-1/3 max-h-[90vh] overflow-y-auto shadow-lg text-white font-sans">
        <h2 class="text-lg sm:text-xl font-title text-[#FACC15] mb-3 sm:mb-4 text-center uppercase">Seleccionar Jugadores Convocados</h2>
        
        <form id="convocatoriaForm">
            @csrf

            <!-- Lista de jugadores con checkboxes -->
            <div class="max-h-[50vh] sm:max-h-60 overflow-y-auto mb-3 sm:mb-4">
                <button type="button" onclick="toggleSelectAll()" class="bg-[#FACC15] text-black px-3 sm:px-4 py-1 sm:py-2 rounded-lg mb-2 sm:mb-3 hover:brightness-110 text-sm sm:text-base w-full sm:w-auto">
                    Seleccionar Todos
                </button>

                @foreach ($team->players as $player)
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="player-{{ $player->id }}" name="convocados[]"
                            value="{{ $player->id }}"
                            class="mr-2 accent-[#00B140] scale-110"
                            {{ isset($convocados) && in_array($player->id, $convocados[$match->id] ?? []) ? 'checked' : '' }}>
                        <label for="player-{{ $player->id }}" class="text-white text-sm sm:text-base">
                            {{ $player->nombre }} {{ $player->apellido }}
                        </label>
                    </div>
                @endforeach
            </div>

            <!-- Botones -->
            <div class="mt-3 sm:mt-4 flex flex-col sm:flex-row gap-2 sm:gap-0 justify-between">
                <button type="button" onclick="saveConvocatoria()" class="bg-[#00B140] text-white px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 w-full sm:mr-2 text-sm sm:text-base">
                    Guardar Convocatoria
                </button>
                <button type="button" onclick="closeModal('convocatoriaModal')" class="bg-[#EF4444] text-white px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 w-full sm:ml-2 text-sm sm:text-base">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>


<script>
function toggleSelectAll() {
    let checkboxes = document.querySelectorAll('input[name="convocados[]"]');
    let allSelected = Array.from(checkboxes).every(checkbox => checkbox.checked);

    checkboxes.forEach(checkbox => {
        checkbox.checked = !allSelected;
    });
}
function openConvocatoriaModal(matchId) {
    // Asegúrate de que exista el input hidden, o créalo si no
    let input = document.querySelector('#convocatoriaForm input[name="match_id"]');
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'match_id';
        document.getElementById('convocatoriaForm').append(input);
    }
    input.value = matchId;

    openModal('convocatoriaModal');
}
</script>