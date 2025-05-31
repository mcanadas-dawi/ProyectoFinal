<!-- Modal para Añadir Jugador -->
<div id="addPlayerModal" class="hidden fixed inset-0 bg-[#1E293B]/80 flex justify-center items-center z-50 p-3 sm:p-0">
    <div class="bg-[#1E3A8A] rounded-lg p-4 sm:p-6 w-full sm:w-4/5 md:w-2/3 lg:w-1/2 xl:w-1/3 shadow-lg text-white font-sans max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg sm:text-xl font-title text-[#FACC15] mb-3 sm:mb-4 text-center uppercase">Añadir Jugador</h2>

        <form id="addPlayerForm" action="{{ route('players.store') }}" method="POST" onsubmit="return validateAddPlayerForm(event)">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">

            <input type="text" id="nombre" name="nombre" placeholder="Nombre" class="w-full p-2 border rounded mb-1 bg-white text-black" required>
            <p id="error-nombre" class="text-red-400 text-sm mb-2 hidden">Solo letras y espacios.</p>

            <input type="text" id="apellido" name="apellido" placeholder="Apellido" class="w-full p-2 border rounded mb-1 bg-white text-black" required>
            <p id="error-apellido" class="text-red-400 text-sm mb-2 hidden">Solo letras y espacios.</p>

            <input type="text" id="dni" name="dni" placeholder="DNI" class="w-full p-2 border rounded mb-1 bg-white text-black" required>
            <p id="error-dni" class="text-red-400 text-sm mb-2 hidden">Debe tener 8 números seguidos de 1 letra. Ej: 12345678A</p>

            <input type="number" id="dorsal" name="dorsal" placeholder="Dorsal" class="w-full p-2 border rounded mb-1 bg-white text-black" required min=1 max=99>
            <p id="error-dorsal" class="text-red-400 text-sm mb-2 hidden">El dorsal debe ser entre 1 y 99.</p>

            <label class="block text-[#FACC15] font-semibold text-sm sm:text-base">Fecha de nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="w-full p-2 border rounded mb-1 bg-white text-black" required>
            <p id="error-fecha" class="text-red-400 text-sm mb-2 hidden">Debe ser una fecha válida (máximo 100 años atrás y no mayor a hoy).</p>

            <label class="block text-[#FACC15] font-semibold text-sm sm:text-base">Posición</label>
            <select name="posicion" required class="w-full p-2 border rounded-lg mb-2 bg-white text-black">
                <option value="Portero">Portero</option>
                <option value="Defensa">Defensa</option>
                <option value="Centrocampista">Centrocampista</option>
                <option value="Delantero">Delantero</option>
            </select>

            <label class="block text-[#FACC15] font-semibold text-sm sm:text-base">Perfil</label>
            <select name="perfil" required class="w-full p-2 border rounded-lg mb-4 bg-white text-black">
                <option value="Diestro">Diestro</option>
                <option value="Zurdo">Zurdo</option>
            </select>

            <div class="flex justify-center space-x-2">
                <button type="submit" class="bg-[#00B140] text-white px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 text-sm sm:text-base">
                    Guardar
                </button>
                <button type="button" onclick="closeModal('addPlayerModal')" class="bg-[#EF4444] text-white px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 text-sm sm:text-base">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("nombre").addEventListener("input", () => validateNombre());
    document.getElementById("apellido").addEventListener("input", () => validateApellido());
    document.getElementById("dni").addEventListener("input", () => validateDNI());
    document.getElementById("dorsal").addEventListener("input", () => validateDorsal());
    document.getElementById("fecha_nacimiento").addEventListener("change", () => validateFechaNacimiento());

    // Limitar calendario
    const fechaInput = document.getElementById("fecha_nacimiento");
    const today = new Date();
    const max = today.toISOString().split("T")[0];
    const min = new Date(today.setFullYear(today.getFullYear() - 100)).toISOString().split("T")[0];
    fechaInput.max = max;
    fechaInput.min = min;
});

function validateNombre() {
    const input = document.getElementById("nombre");
    const error = document.getElementById("error-nombre");
    const regex = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/;

    return toggleValidation(input, error, regex.test(input.value.trim()));
}

function validateApellido() {
    const input = document.getElementById("apellido");
    const error = document.getElementById("error-apellido");
    const regex = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/;

    return toggleValidation(input, error, regex.test(input.value.trim()));
}

function validateDNI() {
    const input = document.getElementById("dni");
    const error = document.getElementById("error-dni");
    const regex = /^\d{8}[A-Za-z]$/;

    return toggleValidation(input, error, regex.test(input.value.trim()));
}

function validateDorsal() {
    const input = document.getElementById("dorsal");
    const error = document.getElementById("error-dorsal");
    const value = parseInt(input.value);

    return toggleValidation(input, error, value >= 1 && value <= 99);
}

function validateFechaNacimiento() {
    const input = document.getElementById("fecha_nacimiento");
    const error = document.getElementById("error-fecha");
    const value = new Date(input.value);
    const today = new Date();
    const min = new Date(today);
    min.setFullYear(today.getFullYear() - 100);

    return toggleValidation(input, error, value >= min && value <= new Date());
}

function toggleValidation(input, errorEl, isValid) {
    if (!isValid) {
        errorEl.classList.remove("hidden");
        input.classList.remove("border-[#00B140]");
        input.classList.add("border-red-500");
        return false;
    } else {
        errorEl.classList.add("hidden");
        input.classList.remove("border-red-500");
        input.classList.add("border-[#00B140]");
        return true;
    }
}

function validateAddPlayerForm(event) {
    const v1 = validateNombre();
    const v2 = validateApellido();
    const v3 = validateDNI();
    const v4 = validateDorsal();
    const v5 = validateFechaNacimiento();

    return v1 && v2 && v3 && v4 && v5;
}
</script>
