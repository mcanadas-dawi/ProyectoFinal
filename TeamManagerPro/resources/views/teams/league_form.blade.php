@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white p-6 rounded-lg w-full max-w-xl mx-auto shadow-lg">
        <h1 class="text-2xl font-semibold text-gray-700 mb-4">Crear Liga</h1>

        {{-- Mensajes de éxito o error --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('rivales_liga.store') }}" method="POST">
            @csrf

            <!-- ID oculto de la plantilla actual -->
            <input type="hidden" name="team_id" value="{{ $team->id }}">

            <!-- Nombre de la Liga -->
            <label class="block mb-1 font-semibold">Nombre de la Liga:</label>
            <input type="text" name="nombre_liga" class="w-full border p-2 rounded mb-3" required>

            <!-- Número de Rivales -->
            <label class="block mb-1 font-semibold">Número de Rivales:</label>
            <input type="number" id="numero_rivales" name="numero_rivales" class="w-full border p-2 rounded mb-3" min="1" required onchange="generarJornadas()">

            <!-- Solo Ida -->
            <div class="flex items-center mb-3">
                <input type="checkbox" id="solo_ida" name="solo_ida" class="mr-2" onchange="generarJornadas()">
                <label for="solo_ida">Solo Ida</label>
            </div>

            <!-- Jornadas Dinámicas -->
            <div id="jornadasContainer" class="space-y-2"></div>

            <!-- Botones de Acción -->
            <div class="flex justify-between mt-4">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Crear Liga
                </button>
                <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
<script>
function generarJornadas() {
    const numeroRivales = parseInt(document.getElementById('numero_rivales').value) || 0;
    const jornadasContainer = document.getElementById('jornadasContainer');
    jornadasContainer.innerHTML = '';

    if (numeroRivales <= 0) {
        alert('Introduce un número válido de rivales.');
        return;
    }

    const soloIda = document.getElementById('solo_ida').checked;

    // Nota sobre marcar como local (solo si es ida y vuelta)
    if (!soloIda) {
        const notaLocal = document.createElement('div');
        notaLocal.className = "bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded mb-4 text-sm";
        notaLocal.innerHTML = `<strong>Nota:</strong> Marca "Local" en las jornadas que tu equipo juegue en casa.`;
        jornadasContainer.appendChild(notaLocal);
    }

    // Generar jornadas (primera vuelta)
    for (let i = 1; i <= numeroRivales; i++) {
        const jornadaDiv = document.createElement('div');
        jornadaDiv.className = "bg-gray-50 border border-gray-200 p-4 rounded-lg shadow-sm mb-3";

        // Label
        const label = document.createElement('label');
        label.className = "block font-medium text-gray-800 mb-2";
        label.textContent = `Jornada ${i}`;
        label.setAttribute('for', `rival_${i}`);

        // Input rival
        const inputRival = document.createElement('input');
        inputRival.type = "text";
        inputRival.name = `rivales[${i}]`;
        inputRival.id = `rival_${i}`;
        inputRival.placeholder = "Nombre del rival";
        inputRival.required = true;
        inputRival.className = "w-full border border-gray-300 p-2 rounded mb-2";

        jornadaDiv.appendChild(label);
        jornadaDiv.appendChild(inputRival);

        // Checkbox "Local" si es ida y vuelta
        if (!soloIda) {
            const checkboxWrapper = document.createElement('div');
            checkboxWrapper.className = "flex items-center";

            const localCheckbox = document.createElement('input');
            localCheckbox.type = "checkbox";
            localCheckbox.name = `local[${i}]`;
            localCheckbox.id = `local_${i}`;
            localCheckbox.className = "mr-2";

            const localLabel = document.createElement('label');
            localLabel.setAttribute('for', `local_${i}`);
            localLabel.textContent = "Local";

            checkboxWrapper.appendChild(localCheckbox);
            checkboxWrapper.appendChild(localLabel);

            jornadaDiv.appendChild(checkboxWrapper);
        }

        jornadasContainer.appendChild(jornadaDiv);
    }

    // Nota sobre la segunda vuelta generada automáticamente
    if (!soloIda) {
        const notaVuelta = document.createElement('div');
        notaVuelta.className = "bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded mt-4 text-sm";
        notaVuelta.innerHTML = `<strong>Nota:</strong> Las jornadas de la segunda vuelta se generarán automáticamente.`;
        jornadasContainer.appendChild(notaVuelta);
    }
}
</script>
@endsection
