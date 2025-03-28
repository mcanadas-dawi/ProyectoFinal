@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Crear Nueva Liga</h2>

    <form action="{{ route('rivales_liga.store') }}" method="POST">
        @csrf
        <label class="block mb-1">Nombre de la Liga:</label>
        <input type="text" name="nombre_liga" class="w-full border p-2 rounded mb-3" required>

        <label class="block mb-1">Número de Rivales:</label>
        <input type="number" id="numero_rivales" class="w-full border p-2 rounded mb-3" min="1" required onchange="generarJornadas()">

        <div class="flex items-center mb-3">
            <input type="checkbox" id="solo_ida" name="solo_ida" class="mr-2" onchange="generarJornadas()">
            <label for="solo_ida">Solo Ida</label>
        </div>

        <div id="jornadasContainer" class="space-y-2"></div>

        <div class="flex justify-between mt-4">
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Crear</button>
            <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</a>
        </div>
    </form>
</div>

<script>
function generarJornadas() {
    const numeroRivales = parseInt(document.getElementById('numero_rivales').value) || 0;
    const soloIda = document.getElementById('solo_ida').checked;
    const jornadasContainer = document.getElementById('jornadasContainer');
    jornadasContainer.innerHTML = '';

    const numJornadas = soloIda ? numeroRivales : numeroRivales * 2;

    for (let i = 1; i <= numJornadas; i++) {
        const jornadaDiv = document.createElement('div');
        jornadaDiv.className = "flex items-center gap-2 mb-2";

        const label = document.createElement('label');
        label.textContent = `Jornada ${i}:`;
        label.className = "block w-32";

        const input = document.createElement('input');
        input.type = "text";
        input.name = `rivales[${i}]`;
        input.placeholder = "Añadir rival";
        input.className = "w-full border p-2 rounded";
        input.required = true;

        jornadaDiv.appendChild(label);
        jornadaDiv.appendChild(input);

        jornadasContainer.appendChild(jornadaDiv);
    }
}
</script>
@endsection
