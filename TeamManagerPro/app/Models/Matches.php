<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id', 'numero_jornada', 'equipo_rival', 'fecha_partido',
        'resultado', 'goles_a_favor', 'goles_en_contra', 'actuacion_equipo'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }    

    public function players() {
        return $this->belongsToMany(Player::class, 'player_match', 'match_id', 'player_id')
                    ->withPivot('valoracion'); 
    }

    public function convocados()
    {
    return $this->belongsToMany(Player::class, 'player_match')->wherePivot('convocado', true);
    }
    
}
