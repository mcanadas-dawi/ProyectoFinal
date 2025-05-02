<!-- Modal para Añadir Partido Amistoso -->
<div id="amistosoModal" class="fixed inset-0 bg-[#1E293B]/80 flex items-center justify-center hidden z-50">
    <div class="bg-[#1E3A8A] p-6 rounded-lg shadow-lg w-96 text-white font-sans">
        <h2 class="text-xl font-title text-[#FACC15] mb-4 text-center uppercase">Añadir Partido Amistoso</h2>

        <form id="amistosoForm" action="{{ route('matches.store') }}" method="POST" onsubmit="return validateAmistosoForm(event)">
            @csrf

            <input type="hidden" name="team_id" value="{{ $team->id }}">
            <input type="hidden" name="tipo" value="amistoso">

            <!-- Equipo Rival -->
            <label for="equipo_rival" class="block text-[#FACC15] font-semibold mb-1">Equipo Rival:</label>
            <input type="text" id="equipo_rival" name="equipo_rival" class="w-full border p-2 rounded mb-1 bg-white text-black" required>
            <p id="error-equipo_rival" class="text-red-400 text-sm mb-2 hidden">Solo se permiten letras y espacios.</p>

            <!-- Fecha -->
            <label for="fecha_partido" class="block text-[#FACC15] font-semibold mb-1">Fecha:</label>
            <input type="date" id="fecha_partido" name="fecha_partido" class="w-full border p-2 rounded mb-1 bg-white text-black" required>
            <p id="error-fecha_partido" class="text-red-400 text-sm mb-2 hidden">La fecha debe estar entre 1 año antes y 1 año después de hoy.</p>

            <!-- Botones -->
            <div class="flex justify-between mt-4">
                <button type="submit" class="bg-[#00B140] text-white px-4 py-2 rounded hover:brightness-110 w-full mr-2">
                    Guardar
                </button>
                <button type="button" onclick="closeModal('amistosoModal')" class="bg-[#EF4444] text-white px-4 py-2 rounded hover:brightness-110 w-full ml-2">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const equipoInput = document.getElementById("equipo_rival");
    const fechaInput = document.getElementById("fecha_partido");

    equipoInput.addEventListener("input", () => validateEquipo());
    fechaInput.addEventListener("change", () => validateFecha());

    // 💡 Limitar el rango del calendario
    const today = new Date();
    const minDate = new Date(today);
    const maxDate = new Date(today);
    minDate.setFullYear(today.getFullYear() - 1);
    maxDate.setFullYear(today.getFullYear() + 1);

    fechaInput.min = minDate.toISOString().split('T')[0];
    fechaInput.max = maxDate.toISOString().split('T')[0];
});

function validateEquipo() {
    const input = document.getElementById("equipo_rival");
    const error = document.getElementById("error-equipo_rival");
    const regex = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/;

    if (!regex.test(input.value.trim())) {
        error.classList.remove("hidden");
        input.classList.remove("border-[#00B140]");
        input.classList.add("border-red-500");
        return false;
    } else {
        error.classList.add("hidden");
        input.classList.remove("border-red-500");
        input.classList.add("border-[#00B140]");
        return true;
    }
}

function validateFecha() {
    const input = document.getElementById("fecha_partido");
    const error = document.getElementById("error-fecha_partido");

    const today = new Date();
    const minDate = new Date(today);
    const maxDate = new Date(today);
    minDate.setFullYear(today.getFullYear() - 1);
    maxDate.setFullYear(today.getFullYear() + 1);

    const fechaValor = new Date(input.value);
    if (isNaN(fechaValor.getTime()) || fechaValor < minDate || fechaValor > maxDate) {
        error.classList.remove("hidden");
        input.classList.remove("border-[#00B140]");
        input.classList.add("border-red-500");
        return false;
    } else {
        error.classList.add("hidden");
        input.classList.remove("border-red-500");
        input.classList.add("border-[#00B140]");
        return true;
    }
}

function validateAmistosoForm(event) {
    const validEquipo = validateEquipo();
    const validFecha = validateFecha();
    return validEquipo && validFecha;
}
</script>
