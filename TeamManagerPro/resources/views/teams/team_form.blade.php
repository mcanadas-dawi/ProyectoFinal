<div class="bg-[#1E3A8A] p-4 rounded-lg mb-4 font-sans text-white shadow-lg">
    <h3 class="text-lg font-title text-[#FACC15] uppercase tracking-wide">Crear Nueva Plantilla</h3>

    <form action="{{ route('teams.store') }}" method="POST" class="mt-2">
        @csrf

        <input type="text" name="nombre" placeholder="Nombre de la plantilla"
            required
            class="w-full p-2 bg-white text-black border rounded-lg mb-2 font-sans">

        <select name="modalidad" required
            class="w-full p-2 bg-white text-black border rounded-lg mb-2 font-sans">
            <option value="F5">F5</option>
            <option value="F7">F7</option>
            <option value="F8">F8</option>
            <option value="F11">F11</option>
        </select>

        <button type="submit"
            class="bg-[#00B140] text-white px-4 py-2 rounded-lg hover:brightness-110 font-sans">
            Crear Plantilla
        </button>
    </form>
</div>
