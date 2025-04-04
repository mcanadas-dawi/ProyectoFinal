<!-- Modal para Convocatoria -->
<div id="convocatoriaModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-1/3">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">Seleccionar Jugadores Convocados</h2>
        <form id="convocatoriaForm">
            @csrf
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
        document.getElementById('convocatoriaForm').appendChild(input);
    }
    input.value = matchId;

    openModal('convocatoriaModal');
}
</script>
