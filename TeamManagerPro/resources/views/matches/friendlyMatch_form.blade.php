<!-- Modal para Añadir Partido Amistoso -->
<div id="amistosoModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Añadir Partido Amistoso</h2>
        <form action="{{ route('matches.store') }}" method="POST">
            @csrf

            <!-- Campo oculto para el ID del equipo -->
            <input type="hidden" name="team_id" value="{{ $team->id }}">

            <label class="block text-gray-700">Equipo Rival:</label>
            <input type="text" name="equipo_rival" class="w-full border p-2 rounded mb-2" required>

            <label class="block text-gray-700">Fecha:</label>
            <input type="date" name="fecha_partido" class="w-full border p-2 rounded mb-2" required>

            <input type="hidden" name="tipo" value="amistoso">

            <div class="flex justify-between mt-4">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Guardar</button>
                <button type="button" onclick="closeModal('amistosoModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            </div>
        </form>
    </div>
</div>