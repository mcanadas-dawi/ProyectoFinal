@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-[#1E293B] text-white font-sans p-3 sm:p-6">
    <h2 class="text-xl sm:text-2xl font-title text-[#FACC15] mb-4 sm:mb-6 uppercase">Valorar Jugadores del Partido</h2>

    <form action="{{ route('matches.saveRatings', $match->id) }}" method="POST">
        @csrf

        <div class="w-full overflow-x-auto mb-6">
            <table class="min-w-[900px] w-full border-collapse">
                <thead>
                    <tr class="bg-[#00B140] text-white">
                        <th class="border border-white p-1 sm:p-3 text-sm sm:text-base w-1/5">Jugador</th>
                        <th class="border border-white p-1 sm:p-3 text-sm sm:text-base">Titular</th>
                        <th class="border border-white p-1 sm:p-3 text-sm sm:text-base">Minutos</th>
                        <th class="border border-white p-1 sm:p-3 text-sm sm:text-base">Goles</th>
                        <th class="border border-white p-1 sm:p-3 text-sm sm:text-base">Goles Encajados</th>
                        <th class="border border-white p-1 sm:p-3 text-sm sm:text-base">Asistencias</th>
                        <th class="border border-white p-1 sm:p-3 text-sm sm:text-base">Amarillas</th>
                        <th class="border border-white p-1 sm:p-3 text-sm sm:text-base">Rojas</th>
                        <th class="border border-white p-1 sm:p-3 text-sm sm:text-base">Valoración</th>
                    </tr>
                </thead>
                <tbody class="bg-[#1E3A8A] text-white">
                    @if (!empty($match->players) && $match->players->count() > 0)
                        @foreach ($match->players as $player)
                            @php
                                $stats = $player->matchStats($match->id);
                                $amarillas = $stats->tarjetas_amarillas ?? 0;
                                $rojas = $stats->tarjetas_rojas ?? 0;
                                $isGoalkeeper = $player->posicion == 'Portero';
                            @endphp
                            <tr class="border border-white" data-player-position="{{ $player->posicion }}">
                                <td class="p-1 sm:p-3 text-sm sm:text-base">{{ $player->nombre }} {{ $player->apellido }} ({{ $player->dorsal }}) - {{ $player->posicion }}</td>

                                <td class="p-1 sm:p-3 text-center">
                                    <input type="checkbox" name="players[{{ $player->id }}][titular]"
                                        value="1" @if($stats && $stats->titular) checked @endif
                                        class="scale-90 sm:scale-125">
                                </td>

                                <td class="p-1 sm:p-3 text-center">
                                    <input type="number" name="players[{{ $player->id }}][minutos_jugados]" min="0" max="120"
                                        class="w-12 sm:w-20 p-1 sm:p-2 rounded text-black text-center text-sm sm:text-base" 
                                        value="{{ $stats->minutos_jugados ?? 0 }}">
                                </td>

                                <td class="p-1 sm:p-3 text-center">
                                    <input type="number" name="players[{{ $player->id }}][goles]" min="0"
                                        class="w-12 sm:w-20 p-1 sm:p-2 rounded text-black text-center text-sm sm:text-base" 
                                        value="{{ $stats->goles ?? 0 }}">
                                </td>

                                <td class="p-1 sm:p-3 text-center">
                                    <input type="number" name="players[{{ $player->id }}][goles_encajados]" min="0"
                                        class="w-12 sm:w-20 p-1 sm:p-2 rounded text-black text-center text-sm sm:text-base" 
                                        value="{{ $stats->goles_encajados ?? 0 }}"
                                        {{ $isGoalkeeper ? '' : 'readonly disabled' }}>
                                </td>
                                <td class="p-1 sm:p-3 text-center">
                                    <input type="number" name="players[{{ $player->id }}][asistencias]" min="0"
                                        class="w-12 sm:w-20 p-1 sm:p-2 rounded text-black text-center text-sm sm:text-base" 
                                        value="{{ $stats->asistencias ?? 0 }}">
                                </td>

                                <td class="p-1 sm:p-3 text-center">
                                    <input type="number" name="players[{{ $player->id }}][tarjetas_amarillas]" min="0" max="2"
                                        class="w-12 sm:w-20 p-1 sm:p-2 rounded text-black text-center tarjetas-amarillas text-sm sm:text-base"
                                        value="{{ $amarillas }}" data-player-id="{{ $player->id }}">
                                </td>

                                <td class="p-1 sm:p-3 text-center">
                                    <input type="number" name="players[{{ $player->id }}][tarjetas_rojas]" min="0" max="1"
                                        class="w-12 sm:w-20 p-1 sm:p-2 rounded text-black text-center tarjetas-rojas text-sm sm:text-base"
                                        value="{{ $rojas }}" data-player-id="{{ $player->id }}"
                                        {{ ($amarillas == 2) ? 'readonly' : '' }}>
                                </td>

                                <td class="p-1 sm:p-3 text-center">
                                    <input type="number" name="players[{{ $player->id }}][valoracion]" min="1" max="10" step="0.1"
                                        class="w-12 sm:w-20 p-1 sm:p-2 rounded text-black text-center text-sm sm:text-base" 
                                        value="{{ $stats->valoracion ?? '' }}">
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="p-3 sm:p-4 text-center text-slate-400 text-sm sm:text-base">No hay jugadores convocados para este partido.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
            <button type="button" onclick="window.history.back();" class="bg-[#EF4444] text-white px-3 sm:px-5 py-2 rounded-lg hover:brightness-110 text-sm sm:text-base order-2 sm:order-1">
                Cancelar
            </button>
            <button type="submit" class="bg-[#00B140] text-white px-3 sm:px-5 py-2 rounded-lg hover:brightness-110 text-sm sm:text-base order-1 sm:order-2">
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

    document.addEventListener('DOMContentLoaded', function() {
    // Verificar que el código se está ejecutando (debug)
    console.log("Script inicializado");
    
    // Manejar los campos de goles encajados para porteros
    const rows = document.querySelectorAll('tr');
    
    rows.forEach(row => {
        // Verificar la posición directamente desde el contexto del jugador
        const golesEncajadosInput = row.querySelector('input[name*="[goles_encajados]"]');
        const isGoalkeeper = row.getAttribute('data-player-position') === 'Portero';
        
        console.log("Fila:", row);
        console.log("Es portero:", isGoalkeeper);
        console.log("Input encontrado:", golesEncajadosInput);
        
        if (golesEncajadosInput) {
            if (isGoalkeeper) {
                golesEncajadosInput.removeAttribute('readonly');
                golesEncajadosInput.removeAttribute('disabled');
                golesEncajadosInput.classList.add('bg-white');
                console.log("Habilitando input para portero");
            } else {
                golesEncajadosInput.setAttribute('readonly', 'readonly');
                golesEncajadosInput.setAttribute('disabled', 'disabled');
                golesEncajadosInput.classList.add('bg-gray-200');
                golesEncajadosInput.value = '0';
                console.log("Deshabilitando input para jugador no portero");
            }
        }
    });
});

</script>
@endsection
