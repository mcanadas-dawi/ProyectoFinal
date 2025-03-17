@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Valorar Jugadores del Partido</h2>
    
    <form action="{{ route('matches.saveRatings', $match->id) }}" method="POST">
        @csrf
        <table class="w-full border-collapse border border-gray-300 mb-4">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Jugador</th>
                    <th class="border p-2">Titular</th>
                    <th class="border p-2">Minutos Jugados</th>
                    <th class="border p-2">Goles</th>
                    <th class="border p-2">Asistencias</th>
                    <th class="border p-2">Tarjetas Amarillas</th>
                    <th class="border p-2">Tarjeta Roja</th>
                    <th class="border p-2">Valoración</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($match->players) && $match->players->count() > 0)
                    @foreach ($match->players as $player)
                        @php
                            $stats = $player->matchStats($match->id);
                            $amarillas = $stats->tarjetas_amarillas ?? 0;
                            $rojas = $stats->tarjetas_rojas ?? 0;
                        @endphp
                        <tr class="border">
                            <td class="p-2">{{ $player->nombre }} {{ $player->apellido }} (Dorsal: {{ $player->dorsal }})</td>

                            <!-- Checkbox para Titular -->
                            <td class="p-2 text-center">
                                <input type="checkbox" name="players[{{ $player->id }}][titular]" 
                                    value="1" 
                                    @if($stats && $stats->titular) checked @endif>
                            </td>

                            <!-- Minutos Jugados -->
                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][minutos_jugados]" min="0" 
                                    class="w-16 p-2 border rounded text-center" 
                                    value="{{ $stats->minutos_jugados ?? 0 }}">
                            </td>

                            <!-- Goles -->
                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][goles]" min="0" 
                                    class="w-16 p-2 border rounded text-center" 
                                    value="{{ $stats->goles ?? 0 }}">
                            </td>

                            <!-- Asistencias -->
                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][asistencias]" min="0" 
                                    class="w-16 p-2 border rounded text-center" 
                                    value="{{ $stats->asistencias ?? 0 }}">
                            </td>

                            <!-- Tarjetas Amarillas -->
                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][tarjetas_amarillas]" min="0" max="2"
                                    class="w-16 p-2 border rounded text-center tarjetas-amarillas" 
                                    value="{{ $amarillas }}" 
                                    data-player-id="{{ $player->id }}">
                            </td>

                            <!-- Tarjeta Roja (Editable si NO hay 2 amarillas) -->
                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][tarjetas_rojas]" min="0" max="1"
                                    class="w-16 p-2 border rounded text-center tarjetas-rojas" 
                                    value="{{ $rojas }}" 
                                    data-player-id="{{ $player->id }}" 
                                    {{ ($amarillas == 2) ? 'readonly' : '' }}>
                            </td>

                            <!-- Valoración -->
                            <td class="p-2 text-center">
                                <input type="number" name="players[{{ $player->id }}][valoracion]" min="1" max="10" step="0.1"
                                    class="w-16 p-2 border rounded text-center" 
                                    value="{{ $stats->valoracion ?? '' }}">
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="p-4 text-center text-gray-500">No hay jugadores convocados para este partido.</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <div class="flex justify-between">
            <button type="button" onclick="window.history.back();" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancelar</button>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Guardar Valoraciones</button>
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
