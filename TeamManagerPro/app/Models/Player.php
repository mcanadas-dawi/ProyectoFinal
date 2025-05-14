<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'apellido', 'dni', 'dorsal', 'fecha_nacimiento',
        'posicion', 'perfil'
    ];

    protected $casts = [
    ];

    // 📌 RELACIONES

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'player_team');
    }

    public function matches()
    {
        return $this->belongsToMany(Matches::class, 'player_match', 'player_id', 'match_id')
                    ->withPivot('valoracion','convocado');
    }

    // 📌 Obtiene la valoración promedio del jugador en una plantilla
    public function getValoracionPorPlantilla($teamId)
    {
        return round($this->matches()
            ->whereHas('team', function ($query) use ($teamId) {
                $query->where('id', $teamId);
            })
            ->avg('player_match.valoracion') ?? 0, 2);
    }

    // 📌 Obtiene las estadísticas de un jugador en un partido específico
    public function matchStats($matchId)
    {
        return $this->hasOne(MatchPlayerStat::class)
                    ->where('match_id', $matchId)
                    ->firstOrNew();
    }

    // 📌 Obtiene las estadísticas de un jugador en una plantilla específica
    public function teamStats($teamId)
    {
        return $this->hasOne(PlayerTeamStats::class)
                    ->where('team_id', $teamId)
                    ->firstOrNew();
    }

    //Obtengo las estadisticas de un jugador SOLO EN LA LIGA
    public function leagueStats($teamId)
    {
        $stats = \App\Models\MatchPlayerStat::where('player_id', $this->id)
            ->whereHas('match', function ($q) use ($teamId) {
                $q->where('team_id', $teamId)
                  ->where('tipo', 'liga');
            });
    
        $partidosJugados = (clone $stats)->count();
    
        $resumen = $stats->selectRaw('
            SUM(minutos_jugados) as minutos_jugados,
            SUM(goles) as goles,
            SUM(goles_encajados) as goles_encajados,
            SUM(asistencias) as asistencias,
            SUM(tarjetas_amarillas) as tarjetas_amarillas,
            SUM(tarjetas_rojas) as tarjetas_rojas,
            SUM(titular) as titular,
            AVG(valoracion) as valoracion
        ')->first();
    
        $resumen->partidos = $partidosJugados;
        $resumen->suplente = $partidosJugados - ($resumen->titular ?? 0);
        $resumen->minutos_por_partido = $partidosJugados > 0
            ? round(($resumen->minutos_jugados ?? 0) / $partidosJugados)
            : 0;
    
        return $resumen;
    }
}