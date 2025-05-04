@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-[#1E293B] text-white font-sans px-8 py-6">
    <div class="bg-[#1E3A8A] p-10 rounded-lg w-full shadow-lg">
        <h1 class="text-3xl font-title text-[#FACC15] mb-8 uppercase">Crear Liga</h1>

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="bg-[#00B140]/10 border border-[#00B140] text-[#00B140] px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-[#EF4444]/10 border border-[#EF4444] text-[#EF4444] px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-[#EF4444]/10 border border-[#EF4444] text-[#EF4444] px-4 py-3 rounded mb-4">
                <strong>Errores:</strong>
                <ul class="list-disc pl-5 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('rivales_liga.store') }}" method="POST" class="w-full">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">

            <!-- Grid de 3 columnas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Nombre -->
                <div class="col-span-1">
                    <label class="block mb-1 font-semibold text-[#FACC15]">Nombre de la Liga:</label>
                    <input type="text" name="nombre_liga" class="w-full border p-2 rounded bg-white text-black" required>
                </div>

                <!-- Número de rivales -->
                <div class="col-span-1">
                    <label class="block mb-1 font-semibold text-[#FACC15]">Número de Rivales:</label>
                    <input type="number" id="numero_rivales" name="numero_rivales"
                        class="w-full border p-2 rounded bg-white text-black"
                        min="1" required onchange="generarJornadas()">
                </div>

                <!-- Ida / Vuelta -->
                <div class="col-span-1 flex items-center pt-6">
                    <input type="hidden" name="solo_ida" value="0">
                    <input type="checkbox" id="solo_ida" name="solo_ida" value="1" class="mr-2 accent-[#00B140]" onchange="generarJornadas()">
                    <label for="solo_ida" class="text-white">Solo Ida</label>
                </div>
            </div>

            <!-- Jornadas dinámicas -->
            <div id="jornadasContainer" class="space-y-4 mt-6 w-full"></div>

            <!-- Botones -->
            <div class="flex justify-end mt-8 space-x-4">
                <a href="{{ route('teams.show', ['team' => $team->id]) }}"
                   class="bg-[#EF4444] text-white px-6 py-3 rounded-lg hover:brightness-110">
                    Cancelar
                </a>
                <button type="submit" class="bg-[#00B140] text-white px-6 py-3 rounded-lg hover:brightness-110">
                    Crear Liga
                </button>
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

    if (!soloIda) {
        const notaLocal = document.createElement('div');
        notaLocal.className = "bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded mb-4 text-sm";
        notaLocal.innerHTML = `<strong>Nota:</strong> Marca "Local" en las jornadas que tu equipo juegue en casa.`;
        jornadasContainer.append(notaLocal);
    }

    for (let i = 1; i <= numeroRivales; i++) {
        const jornadaDiv = document.createElement('div');
        jornadaDiv.className = "bg-gray-50 border border-gray-200 p-4 rounded-lg shadow-sm mb-3";

        const label = document.createElement('label');
        label.className = "block font-medium text-gray-800 mb-2";
        label.textContent = `Jornada ${i}`;
        label.setAttribute('for', `rival_${i}`);

        const inputRival = document.createElement('input');
        inputRival.type = "text";
        inputRival.name = `rivales[${i}]`;
        inputRival.id = `rival_${i}`;
        inputRival.placeholder = "Nombre del rival";
        inputRival.required = true;
        inputRival.className = "text-gray-800 dark:text-white";

        jornadaDiv.append(label);
        jornadaDiv.append(inputRival);

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
            localLabel.className = "text-gray-800 dark:text-white";
            checkboxWrapper.append(localCheckbox);
            checkboxWrapper.append(localLabel);

            jornadaDiv.append(checkboxWrapper);
        }

        jornadasContainer.append(jornadaDiv);
    }

    if (!soloIda) {
        const notaVuelta = document.createElement('div');
        notaVuelta.className = "bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded mt-4 text-sm";
        notaVuelta.innerHTML = `<strong>Nota:</strong> Las jornadas de la segunda vuelta se generarán automáticamente.`;
        jornadasContainer.append(notaVuelta);
    }
}
</script>
@endsection
