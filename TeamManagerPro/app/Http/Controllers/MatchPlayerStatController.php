<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchPlayerStat;
use App\Models\Player;
use App\Models\Matches;
use App\Models\Team;
use App\Models\PlayerTeamStats;
use Illuminate\Support\Facades\Log;

class MatchPlayerStatController extends Controller
{
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

        $match = Matches::findOrFail($matchId);
        
        $playerId = $request->player_id;
        $tarjetasRojas = ($request->tarjetas_amarillas == 2) ? 1 : ($request->tarjetas_rojas ?? 0);

        // Guardar o actualizar estadísticas del jugador en `match_player_stats`
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

    // 📌 Vista para valorar jugadores
    public function ratePlayers($matchId) 
    {
        try {
            $match = Matches::with(['players' => function($query) {
                $query->wherePivot('convocado', true);
            }])->findOrFail($matchId);
    
            return view('players.rate_players', compact('match'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudieron cargar los jugadores para valoración.');
        }
    }
    

    public function saveRatings(Request $request, $matchId) 
    {

        $match = Matches::findOrFail($matchId);
        $teamId = $match->team_id; // Obtener el equipo del partido

        if ($request->has('players')) {
            foreach ($request->players as $playerId => $data) {
                $this->procesarValoracionJugador($playerId, $matchId, $teamId, $data);
            }
        }

        return redirect()->route('teams.show', $match->team_id)->with('success', 'Valoraciones guardadas correctamente.');
    }

    private function procesarValoracionJugador($playerId, $matchId, $teamId, $data)
    {
        $player = Player::findOrFail($playerId);
        $esTitular = isset($data['titular']) ? 1 : 0;
        $esSuplente = $esTitular ? 0 : 1;
        $amarillas = $data['tarjetas_amarillas'] ?? 0;
        $rojas = ($amarillas == 2) ? 1 : ($data['tarjetas_rojas'] ?? 0);
        $valoracion = $data['valoracion'] ?? null;
        $golesEncajados = ($player->posicion == 'Portero') ? ($data['goles_encajados'] ?? 0) : 0; // Solo para porteros

        // Obtener estadísticas del jugador en el partido
        $existingMatchStats = MatchPlayerStat::where('match_id', $matchId)
            ->where('player_id', $playerId)
            ->first();

        // Obtener estadísticas acumuladas en la plantilla
        $stats = PlayerTeamStats::firstOrCreate([
            'player_id' => $playerId,
            'team_id' => $teamId
        ]);

        if ($existingMatchStats) {
            $this->restarEstadisticasPrevias($stats, $existingMatchStats);
            $existingMatchStats->update([
                'minutos_jugados' => $data['minutos_jugados'] ?? 0,
                'goles' => $data['goles'] ?? 0,
                'goles_encajados' => $golesEncajados,
                'asistencias' => $data['asistencias'] ?? 0,
                'tarjetas_amarillas' => $amarillas,
                'tarjetas_rojas' => $rojas,
                'valoracion' => $valoracion,
                'titular' => $esTitular,
                'suplente' => $esSuplente
            ]);
        } else {
            MatchPlayerStat::create([
                'match_id' => $matchId,
                'player_id' => $playerId,
                'minutos_jugados' => $data['minutos_jugados'] ?? 0,
                'goles' => $data['goles'] ?? 0,
                'goles_encajados' => $golesEncajados,
                'asistencias' => $data['asistencias'] ?? 0,
                'tarjetas_amarillas' => $amarillas,
                'tarjetas_rojas' => $rojas,
                'valoracion' => $valoracion,
                'titular' => $esTitular,
                'suplente' => $esSuplente
            ]);
        }

        $this->sumarNuevasEstadisticas($stats, $data, $amarillas, $rojas, $esTitular, $esSuplente, $playerId, $teamId, $golesEncajados);
    }

    private function restarEstadisticasPrevias($stats, $existingMatchStats)
    {
        $stats->update([
            'minutos_jugados' => max(0, $stats->minutos_jugados - $existingMatchStats->minutos_jugados),
            'goles' => max(0, $stats->goles - $existingMatchStats->goles),
            'goles_encajados' => max(0, $stats->goles_encajados - $existingMatchStats->goles_encajados),
            'asistencias' => max(0, $stats->asistencias - $existingMatchStats->asistencias),
            'tarjetas_amarillas' => max(0, $stats->tarjetas_amarillas - $existingMatchStats->tarjetas_amarillas),
            'tarjetas_rojas' => max(0, $stats->tarjetas_rojas - $existingMatchStats->tarjetas_rojas),
            'titular' => max(0, $stats->titular - $existingMatchStats->titular),
            'suplente' => max(0, $stats->suplente - $existingMatchStats->suplente),
        ]);
    }

    private function sumarNuevasEstadisticas($stats, $data, $amarillas, $rojas, $esTitular, $esSuplente, $playerId, $teamId, $golesEncajados)

    {
        $stats->update([
            'minutos_jugados' => $stats->minutos_jugados + ($data['minutos_jugados'] ?? 0),
            'goles' => $stats->goles + ($data['goles'] ?? 0),
            'goles_encajados' => $stats->goles_encajados + $golesEncajados,
            'asistencias' => $stats->asistencias + ($data['asistencias'] ?? 0),
            'tarjetas_amarillas' => $stats->tarjetas_amarillas + $amarillas,
            'tarjetas_rojas' => $stats->tarjetas_rojas + $rojas,
            'titular' => $stats->titular + $esTitular,
            'suplente' => $stats->suplente + $esSuplente,
            'valoracion' => $this->calcularValoracionMedia($playerId, $teamId)
        ]);
    }
    private function calcularValoracionMedia($playerId, $teamId)
{
    // Obtener todas las valoraciones del jugador en la plantilla
    $valoraciones = MatchPlayerStat::whereHas('match', function ($query) use ($teamId) {
        $query->where('team_id', $teamId);
    })->where('player_id', $playerId)
      ->whereNotNull('valoracion') // Solo contar valoraciones que no sean nulas
      ->pluck('valoracion');

    // Si no hay valoraciones, devolver 0
    if ($valoraciones->isEmpty()) {
        return 0;
    }

    // Calcular el promedio de valoraciones
    return round($valoraciones->avg(), 2);
}

}
