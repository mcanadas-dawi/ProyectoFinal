@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-[#1E293B] text-white font-sans p-6">
    <h2 class="text-2xl font-title text-[#FACC15] mb-6 uppercase">Valorar Jugadores del Partido</h2>

    <form action="{{ route('matches.saveRatings', $match->id) }}" method="POST">
        @csrf

        <table class="w-full border-collapse mb-6">
            <thead>
                <tr class="bg-[#00B140] text-white">
                    <th class="border border-white p-2">Jugador</th>
                    <th class="border border-white p-2">Titular</th>
                    <th class="border border-white p-2">Minutos</th>
                    <th class="border border-white p-2">Goles</th>
                    <th class="border border-white p-2">Asistencias</th>
                    <th class="border border-white p-2">Amarillas</th>
                    <th class="border border-white p-2">Rojas</th>
                    <th class="border border-white p-2">Valoración</th>
                </tr>
            </thead>
            <tbody class="bg-[#1E3A8A] text-white">
                @if (!empty($match->players) && $match->players->count() > 0)
                    @foreach ($match->players as $player)
                        @php
                            $stats = $player->matchStats($match->id);
                            $amarillas = $stats->tarjetas_amarillas ?? 0;
                            $rojas = $stats->tarjetas_rojas ?? 0;
                        @endphp
                        <tr class="border border-white">
                            <td class="p-2">{{ $player->nombre }} {{ $player->apellido }} (Dorsal: {{ $player->dorsal }})</td>

                            <td class="p-2 text-center">
                                <input type="checkbox" name="players[{{ $player->id }}][titular]"
                                    value="1" @if($stats && $stats->titular) checked @endif>
                            </td>

                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][minutos_jugados]" min="0" max="120"
                                    class="w-16 p-2 rounded text-black text-center" value="{{ $stats->minutos_jugados ?? 0 }}">
                            </td>

                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][goles]" min="0"
                                    class="w-16 p-2 rounded text-black text-center" value="{{ $stats->goles ?? 0 }}">
                            </td>

                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][asistencias]" min="0"
                                    class="w-16 p-2 rounded text-black text-center" value="{{ $stats->asistencias ?? 0 }}">
                            </td>

                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][tarjetas_amarillas]" min="0" max="2"
                                    class="w-16 p-2 rounded text-black text-center tarjetas-amarillas"
                                    value="{{ $amarillas }}" data-player-id="{{ $player->id }}">
                            </td>

                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][tarjetas_rojas]" min="0" max="1"
                                    class="w-16 p-2 rounded text-black text-center tarjetas-rojas"
                                    value="{{ $rojas }}" data-player-id="{{ $player->id }}"
                                    {{ ($amarillas == 2) ? 'readonly' : '' }}>
                            </td>

                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][valoracion]" min="1" max="10" step="0.1"
                                    class="w-16 p-2 rounded text-black text-center" value="{{ $stats->valoracion ?? '' }}">
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="p-4 text-center text-slate-400">No hay jugadores convocados para este partido.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="flex justify-between">
            <button type="button" onclick="window.history.back();" class="bg-[#EF4444] text-white px-4 py-2 rounded-lg hover:brightness-110">
                Cancelar
            </button>
            <button type="submit" class="bg-[#00B140] text-white px-4 py-2 rounded-lg hover:brightness-110">
                Guardar Valoraciones
            </button>
        </div>
    </form>
</div>


<!-- JavaScript para actualizar automáticamente la tarjeta roja según las amarillas -->
<script>
    let tarjetasAmarillasInputs = document.querySelectorAll(".tarjetas-amarillas");

    tarjetasAmarillasInputs.forEach(input => {
        input.addEventListener("change", function () {
            let playerId = this.dataset.playerId;
            let tarjetasRojaInput = document.querySelector(`.tarjetas-rojas[data-player-id='${playerId}']`);
            
            if (this.value == 2) {
                tarjetasRojaInput.value = 1;
                tarjetasRojaInput.setAttribute("readonly", "readonly"); // Bloquear edición
            } else {
                tarjetasRojaInput.removeAttribute("readonly"); // Permitir edición
                tarjetasRojaInput.value = 0; // Resetear a 0 si no tiene 2 amarillas
            }
        });
    });

</script>
@endsection
