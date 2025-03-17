<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerTeamStats extends Model
{
    use HasFactory;

    protected $table = 'player_team_stats'; // Nombre correcto de la tabla

    protected $fillable = [
        'player_id',
        'team_id',
        'minutos_jugados',
        'goles',
        'asistencias',
        'tarjetas_amarillas',
        'tarjetas_rojas',
        'titular',
        'suplente',
        'valoracion'
    ];

    /**
     * Relación con el modelo Player.
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Relación con el modelo Team.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
