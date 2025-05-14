<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerTeamStats extends Model
{
    use HasFactory;

    protected $table = 'player_team_stats';

    protected $fillable = [
        'player_id',
        'team_id',
        'minutos_jugados',
        'goles',
        'goles_encajados',
        'asistencias',
        'tarjetas_amarillas',
        'tarjetas_rojas',
        'titular',
        'suplente',
        'valoracion'
    ];

    protected $casts = [
        'minutos_jugados' => 'integer',
        'goles' => 'integer',
        'goles_encajados' => 'integer',
        'asistencias' => 'integer',
        'tarjetas_amarillas' => 'integer',
        'tarjetas_rojas' => 'integer',
        'titular' => 'integer',
        'suplente' => 'integer',
        'valoracion' => 'decimal:2',
    ];

    // 📌 RELACIONES

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
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
    
    public function getGolesEncajadosAttribute($value)
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

