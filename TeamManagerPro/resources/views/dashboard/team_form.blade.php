<div class="bg-gray-100 p-4 rounded-lg mb-4">
    <h3 class="text-2xl font-semibold text-green-600">Crear Nueva Plantilla</h3>
    <form action="{{ route('teams.store') }}" method="POST" class="mt-2">
    @csrf
    <input type="text" name="nombre" placeholder="Nombre de la plantilla" required class="w-full p-2 border rounded-lg mb-2">
    <select name="modalidad" required class="w-full p-2 border rounded-lg mb-2">
        <option value="F5">F5</option>
        <option value="F7">F7</option>
        <option value="F8">F8</option>
        <option value="F11">F11</option>
    </select>
    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Crear Plantilla</button>
</form>
