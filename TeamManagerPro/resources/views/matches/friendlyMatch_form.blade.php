<!-- Modal para Añadir Partido Amistoso -->
<div id="amistosoModal" class="fixed inset-0 bg-[#1E293B]/80 flex items-center justify-center hidden z-50">
    <div class="bg-[#1E3A8A] p-6 rounded-lg shadow-lg w-96 text-white font-sans">
        <h2 class="text-xl font-title text-[#FACC15] mb-4 text-center uppercase">Añadir Partido Amistoso</h2>
        
        <form action="{{ route('matches.store') }}" method="POST">
            @csrf

            <!-- Campo oculto para el ID del equipo -->
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            <input type="hidden" name="tipo" value="amistoso">

            <label class="block text-[#FACC15] font-semibold mb-1">Equipo Rival:</label>
            <input type="text" name="equipo_rival" class="w-full border p-2 rounded mb-3 bg-white text-black" required>

            <label class="block text-[#FACC15] font-semibold mb-1">Fecha:</label>
            <input type="date" name="fecha_partido" class="w-full border p-2 rounded mb-4 bg-white text-black" required>

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
