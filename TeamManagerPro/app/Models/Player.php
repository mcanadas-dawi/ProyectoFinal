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
        'suplente', 'valoracion'
    ];

    // Un jugador puede pertenecer a múltiples plantillas
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'player_team');
    }

    public function matches() {
    return $this->belongsToMany(Matches::class, 'player_match', 'player_id', 'match_id')
                ->withPivot('valoracion'); 
}

// Calcula la media de valoraciones del jugador
public function getValoracionPorPlantilla($teamId)
{
    return $this->matches()
        ->whereHas('team', function ($query) use ($teamId) {
            $query->where('id', $teamId);
        })
        ->avg('player_match.valoracion') ?? 0;
}
}

