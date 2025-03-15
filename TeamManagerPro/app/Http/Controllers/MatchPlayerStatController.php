<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchPlayerStat;
use App\Models\Player;
use App\Models\Matches;

class MatchPlayerStatController extends Controller {
    public function store(Request $request, $matchId)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'titular' => 'nullable|boolean',
            'minutos_jugados' => 'nullable|integer|min:0',
            'goles' => 'nullable|integer|min:0',
            'asistencias' => 'nullable|integer|min:0',
            'tarjetas_amarillas' => 'nullable|integer|min:0|max:2',
            'tarjetas_rojas' => 'nullable|integer|min:0|max:1'
        ]);
    
        $playerId = $request->player_id;
        $match = Matches::findOrFail($matchId);
        $player = Player::findOrFail($playerId);
    
        // Calcular tarjeta roja si hay 2 amarillas
        $tarjetasRojas = ($request->tarjetas_amarillas == 2) ? 1 : ($request->tarjetas_rojas ?? 0);
    
        // **Guardar o actualizar estadísticas del jugador en `match_player_stats`**
        MatchPlayerStat::updateOrCreate(
            [
                'match_id' => $matchId,
                'player_id' => $playerId
            ],
            [
                'minutos_jugados' => $request->minutos_jugados ?? 0,
                'goles' => $request->goles ?? 0,
                'asistencias' => $request->asistencias ?? 0,
                'tarjetas_amarillas' => $request->tarjetas_amarillas ?? 0,
                'tarjetas_rojas' => $tarjetasRojas
            ]
        );
    
        return response()->json(['message' => 'Estadísticas actualizadas con éxito']);
    }
    
        
}