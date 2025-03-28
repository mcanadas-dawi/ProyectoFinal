<!-- 📌 Modal para añadir partido de liga -->
<div id="ligaModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Añadir Partido de Liga</h2>
        <form action="{{ route('matches.store') }}" method="POST">
            @csrf
            <input type="hidden" name="tipo" value="liga">

            <label class="block text-gray-700">Seleccionar Rival:</label>
            <select name="rival_liga_id" class="w-full border p-2 rounded mb-2" required>
                @if(isset($rivales) && count($rivales) > 0)
                    @foreach ($rivales as $rival)
                        <option value="{{ $rival->id }}">{{ $rival->nombre_equipo }} - Jornada {{ $rival->jornada }}</option>
                    @endforeach
                @else
                    <option value="" disabled>No hay rivales disponibles</option>
                @endif
            </select>


            <label class="block text-gray-700">Fecha:</label>
            <input type="date" name="fecha_partido" class="w-full border p-2 rounded mb-2" required>

            <div class="flex justify-between mt-4">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Guardar</button>
                <button type="button" onclick="closeModal('ligaModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            </div>
        </form>
    </div>
</div>