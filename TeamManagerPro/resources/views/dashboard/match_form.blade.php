<div class="bg-gray-100 p-4 rounded-lg mt-4">
    <h3 class="text-lg font-semibold text-gray-800">Añadir Partido</h3>
    <form action="{{ route('matches.store') }}" method="POST" class="mt-2">
        @csrf
        <input type="hidden" name="team_id" value="{{ $team->id }}">
        <input type="text" name="opponent" placeholder="Rival" required
            class="w-full p-2 border rounded-lg mb-2">
        <input type="date" name="date" required class="w-full p-2 border rounded-lg mb-2">
        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg">Añadir Partido</button>
    </form>
</div>