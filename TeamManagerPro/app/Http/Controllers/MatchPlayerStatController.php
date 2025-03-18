<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchPlayerStat;
use App\Models\Player;
use App\Models\Matches;
use App\Models\RivalLiga;
use App\Models\Team;
use App\Models\PlayerTeamStats;
use Illuminate\Support\Facades\Log;



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
        
        // Evitar registrar estadísticas si el partido es amistoso
        if ($match->tipo === 'amistoso') {
            return response()->json(['message' => 'No se registran estadísticas para partidos amistosos'], 403);
        }
        
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

    // Vista para valorar jugadores
public function ratePlayers($matchId) {
    $match = Matches::with(['convocados'])->findOrFail($matchId);

    return view('matches.rate_players', compact('match'));
}

public function saveRatings(Request $request, $matchId) 
{
    Log::info('Guardando valoraciones', ['match_id' => $matchId, 'players' => $request->players]);

    $match = Matches::findOrFail($matchId);
    $teamId = $match->team_id; // Obtener el equipo del partido

    if ($request->has('players')) {
        foreach ($request->players as $playerId => $data) {
            $player = Player::findOrFail($playerId);

            $esTitular = isset($data['titular']) ? 1 : 0;
            $esSuplente = $esTitular ? 0 : 1;

            // 📌 **Determinar las tarjetas amarillas**
            $amarillas = $data['tarjetas_amarillas'] ?? 0;

            // 📌 **Si el jugador tiene 2 amarillas, recibe automáticamente 1 roja (NO EDITABLE)**
            if ($amarillas == 2) {
                $rojas = 1;
            } else {
                // 📌 **Si tiene 0 o 1 amarilla, la roja es editable**
                $rojas = $data['tarjetas_rojas'] ?? 0;
            }

            $valoracion = $data['valoracion'] ?? null;

            // 📌 **Obtener estadísticas del jugador en el partido**
            $existingMatchStats = MatchPlayerStat::where('match_id', $matchId)
                ->where('player_id', $playerId)
                ->first();

            // 📌 **Obtener estadísticas acumuladas en la plantilla**
            $stats = PlayerTeamStats::firstOrCreate([
                'player_id' => $playerId,
                'team_id' => $teamId
            ]);

            if ($existingMatchStats) {
                // 📌 **Restar los valores antiguos antes de actualizar**
                $stats->update([
                    'minutos_jugados' => max(0, $stats->minutos_jugados - $existingMatchStats->minutos_jugados),
                    'goles' => max(0, $stats->goles - $existingMatchStats->goles),
                    'asistencias' => max(0, $stats->asistencias - $existingMatchStats->asistencias),
                    'tarjetas_amarillas' => max(0, $stats->tarjetas_amarillas - $existingMatchStats->tarjetas_amarillas),
                    'tarjetas_rojas' => max(0, $stats->tarjetas_rojas - $existingMatchStats->tarjetas_rojas),
                    'titular' => max(0, $stats->titular - $existingMatchStats->titular),
                    'suplente' => max(0, $stats->suplente - $existingMatchStats->suplente),
                ]);

                // 📌 **Actualizar los valores en `match_player_stats`**
                $existingMatchStats->update([
                    'minutos_jugados' => $data['minutos_jugados'] ?? 0,
                    'goles' => $data['goles'] ?? 0,
                    'asistencias' => $data['asistencias'] ?? 0,
                    'tarjetas_amarillas' => $amarillas,
                    'tarjetas_rojas' => $rojas, // 📌 **Aplica la nueva lógica de tarjetas rojas**
                    'valoracion' => $valoracion,
                    'titular' => $esTitular,
                    'suplente' => $esSuplente
                ]);
            } else {
                // 📌 **Si el partido es nuevo, crear entrada y sumar a `player_team_stats`**
                MatchPlayerStat::create([
                    'match_id' => $matchId,
                    'player_id' => $playerId,
                    'minutos_jugados' => $data['minutos_jugados'] ?? 0,
                    'goles' => $data['goles'] ?? 0,
                    'asistencias' => $data['asistencias'] ?? 0,
                    'tarjetas_amarillas' => $amarillas,
                    'tarjetas_rojas' => $rojas,
                    'valoracion' => $valoracion,
                    'titular' => $esTitular,
                    'suplente' => $esSuplente
                ]);
            }

            // 📌 **Sumar los nuevos valores en `player_team_stats` después de actualizar `match_player_stats`**
            $stats->update([
                'minutos_jugados' => $stats->minutos_jugados + ($data['minutos_jugados'] ?? 0),
                'goles' => $stats->goles + ($data['goles'] ?? 0),
                'asistencias' => $stats->asistencias + ($data['asistencias'] ?? 0),
                'tarjetas_amarillas' => $stats->tarjetas_amarillas + $amarillas,
                'tarjetas_rojas' => $stats->tarjetas_rojas + $rojas,
                'titular' => $stats->titular + $esTitular,
                'suplente' => $stats->suplente + $esSuplente,
                'valoracion' => $this->calcularValoracionMedia($playerId, $teamId)
            ]);
        }
    }

    return redirect()->route('teams.show', $match->team_id)->with('success', 'Valoraciones guardadas correctamente.');
}
private function calcularValoracionMedia($playerId, $teamId)
{
    // Obtener todas las valoraciones del jugador en la plantilla
    $valoraciones = MatchPlayerStat::whereHas('match', function ($query) use ($teamId) {
        $query->where('team_id', $teamId);
    })->where('player_id', $playerId)
      ->whereNotNull('valoracion')
      ->pluck('valoracion');

    // Si no hay valoraciones, devolver 0
    if ($valoraciones->isEmpty()) {
        return 0;
    }

    // Calcular el promedio de valoraciones
    return round($valoraciones->avg(), 2);
}

}