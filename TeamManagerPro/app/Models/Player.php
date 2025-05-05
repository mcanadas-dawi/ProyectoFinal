<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'apellido', 'dni', 'dorsal', 'fecha_nacimiento',
        'posicion', 'perfil', 'minutos_jugados', 'goles',
        'asistencias', 'goles_encajados', 'titular',
        'suplente', 'valoracion', 'tarjetas_amarillas', 'tarjetas_rojas'
    ];

    protected $casts = [
        'minutos_jugados' => 'integer',
        'goles' => 'integer',
        'asistencias' => 'integer',
        'goles_encajados' => 'integer',
        'titular' => 'integer',
        'suplente' => 'integer',
        'valoracion' => 'decimal:2',
        'tarjetas_amarillas' => 'integer',
        'tarjetas_rojas' => 'integer',
    ];

    // 📌 RELACIONES

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'player_team');
    }

    public function matches()
    {
        return $this->belongsToMany(Matches::class, 'player_match', 'player_id', 'match_id')
                    ->withPivot('valoracion','convocado','posicion')
                    ->withTimestamps();
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
    
    


    // 📌 MUTATORS Y ACCESSORS

    public function getMinutosJugadosAttribute($value)
    {
        return $value ?? 0;
    }

    public function getGolesAttribute($value)
    {
        return $value ?? 0;
    }

    public function getAsistenciasAttribute($value)
    {
        return $value ?? 0;
    }

    public function getTarjetasAmarillasAttribute($value)
    {
        return min($value ?? 0, 2); // Nunca más de 2 amarillas
    }

    public function getTarjetasRojasAttribute($value)
    {
        return min($value ?? 0, 1); // Nunca más de 1 roja
    }

    public function getValoracionAttribute($value)
    {
        return $value ?? 0.0;
    }
}

