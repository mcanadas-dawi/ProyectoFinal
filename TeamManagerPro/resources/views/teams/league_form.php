<!-- 📌 Modal para Crear Liga -->
<div id="addLeagueModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-6 rounded-lg w-96">
        <form action="{{ route('ligas.store') }}" method="POST">
            <!-- Nombre de la Liga -->
            <label class="block mb-1">Nombre de la Liga:</label>
            <input type="text" name="nombre_liga" class="w-full border p-2 rounded mb-3" required>

            <!-- Número de Rivales -->
            <label class="block mb-1">Número de Rivales:</label>
            <input type="number" id="numero_rivales" class="w-full border p-2 rounded mb-3" min="1" required onchange="generarJornadas()">

            <!-- Solo Ida -->
            <div class="flex items-center mb-3">
                <input type="checkbox" id="solo_ida" class="mr-2" onchange="generarJornadas()">
                <label for="solo_ida">Solo Ida</label>
            </div>

            <!-- Jornadas Dinámicas -->
            <div id="jornadasContainer" class="space-y-2"></div>

            <!-- Botones de Acción -->
            <div class="flex justify-between mt-4">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Crear</button>
                <button type="button" onclick="closeModal('createLeagueModal')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function generarJornadas() {
    const numeroRivales = parseInt(document.getElementById('numero_rivales').value) || 0;
    const soloIda = document.getElementById('solo_ida').checked;
    const jornadasContainer = document.getElementById('jornadasContainer');
    jornadasContainer.innerHTML = '';

    let numJornadas = soloIda ? numeroRivales : numeroRivales * 2;
    let segundaVuelta = !soloIda;

    for (let i = 1; i <= numJornadas; i++) {
        const jornadaDiv = document.createElement('div');
        jornadaDiv.className = "flex items-center gap-2";

        // Etiqueta de la jornada
        const label = document.createElement('label');
        label.className = "block mb-1";
        label.textContent = `Jornada ${i}:`;

        // Input de rival
        const input = document.createElement('input');
        input.type = "text";
        input.name = `rivales[${i}]`;
        input.placeholder = "Añadir rival";
        input.className = "w-full border p-2 rounded";

        jornadaDiv.appendChild(label);
        jornadaDiv.appendChild(input);

        // Checkbox para ida y vuelta (local/visitante)
        if (segundaVuelta) {
            const localCheckbox = document.createElement('input');
            localCheckbox.type = "checkbox";
            localCheckbox.name = `local[${i}]`;
            localCheckbox.className = "ml-2";
            const localLabel = document.createElement('label');
            localLabel.textContent = "Local";

            jornadaDiv.appendChild(localCheckbox);
            jornadaDiv.appendChild(localLabel);
        }

        jornadasContainer.appendChild(jornadaDiv);

        // Invertir la condición de local/visitante después de la primera vuelta
        if (i === numeroRivales) {
            segundaVuelta = false;
        }
    }
}
</script>