<div id="addPlayerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-1/3">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Añadir Jugador</h2>
        <form action="{{ route('players.store') }}" method="POST">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">

            <input type="text" name="nombre" placeholder="Nombre" class="w-full p-2 border rounded mb-2" required>
            <input type="text" name="apellido" placeholder="Apellido" class="w-full p-2 border rounded mb-2" required>
            <input type="text" name="dni" placeholder="DNI" class="w-full p-2 border rounded mb-2" required>
            <input type="number" name="dorsal" placeholder="Dorsal" class="w-full p-2 border rounded mb-2" required>

            <label class="block text-gray-700 font-semibold">Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="w-full p-2 border rounded mb-2" required>

            <label class="block text-gray-700 font-semibold">Posición</label>
            <select name="posicion" required class="w-full p-2 border rounded-lg mb-2">
                <option value="Portero">Portero</option>
                <option value="Defensa">Defensa</option>
                <option value="Centrocampista">Centrocampista</option>
                <option value="Delantero">Delantero</option>
            </select>

            <label class="block text-gray-700 font-semibold">Perfil</label>
            <select name="perfil" required class="w-full p-2 border rounded-lg mb-2">
                <option value="Diestro">Diestro</option>
                <option value="Zurdo">Zurdo</option>
            </select>

            <div class="flex justify-end space-x-2 mt-4">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Guardar</button>
                <button type="button" onclick="closeModal('addPlayerModal')" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancelar</button>
            </div>
        </form>
    </div>
</div>