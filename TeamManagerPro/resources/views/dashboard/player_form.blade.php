<div class="bg-gray-100 p-4 rounded-lg mt-4">
    <h3 class="text-lg font-semibold text-gray-800">Añadir Jugador</h3>
    <form action="{{ route('players.store') }}" method="POST" class="mt-2">
        @csrf
        <input type="hidden" name="team_id" value="{{ $team->id }}">
        <input type="text" name="name" placeholder="Nombre del jugador" required
            class="w-full p-2 border rounded-lg mb-2">
        <input type="text" name="position" placeholder="Posición" required
            class="w-full p-2 border rounded-lg mb-2">
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Añadir</button>
    </form>
</div>