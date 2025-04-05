<!-- Modal para Añadir Jugador -->
<div id="addPlayerModal" class="hidden fixed inset-0 bg-[#1E293B]/80 flex justify-center items-center z-50">
    <div class="bg-[#1E3A8A] rounded-lg p-6 w-1/3 shadow-lg text-white font-sans">
        <h2 class="text-xl font-title text-[#FACC15] mb-4 text-center uppercase">Añadir Jugador</h2>

        <form action="{{ route('players.store') }}" method="POST">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">

            <input type="text" name="nombre" placeholder="Nombre" class="w-full p-2 border rounded mb-2 bg-white text-black" required>
            <input type="text" name="apellido" placeholder="Apellido" class="w-full p-2 border rounded mb-2 bg-white text-black" required>
            <input type="text" name="dni" placeholder="DNI" class="w-full p-2 border rounded mb-2 bg-white text-black" required>
            <input type="number" name="dorsal" placeholder="Dorsal" class="w-full p-2 border rounded mb-2 bg-white text-black" required>

            <label class="block text-[#FACC15] font-semibold">Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="w-full p-2 border rounded mb-2 bg-white text-black" required>

            <label class="block text-[#FACC15] font-semibold">Posición</label>
            <select name="posicion" required class="w-full p-2 border rounded-lg mb-2 bg-white text-black">
                <option value="Portero">Portero</option>
                <option value="Defensa">Defensa</option>
                <option value="Centrocampista">Centrocampista</option>
                <option value="Delantero">Delantero</option>
            </select>

            <label class="block text-[#FACC15] font-semibold">Perfil</label>
            <select name="perfil" required class="w-full p-2 border rounded-lg mb-4 bg-white text-black">
                <option value="Diestro">Diestro</option>
                <option value="Zurdo">Zurdo</option>
            </select>

            <div class="flex justify-end space-x-2">
                <button type="submit" class="bg-[#00B140] text-white px-4 py-2 rounded-lg hover:brightness-110">
                    Guardar
                </button>
                <button type="button" onclick="closeModal('addPlayerModal')" class="bg-[#EF4444] text-white px-4 py-2 rounded-lg hover:brightness-110">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
