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
}
