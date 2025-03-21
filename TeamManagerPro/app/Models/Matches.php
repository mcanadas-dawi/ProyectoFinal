<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id', 'rival_liga_id', 'tipo', 'equipo_rival', 'fecha_partido',
        'resultado', 'goles_a_favor', 'goles_en_contra', 'actuacion_equipo'
    ];

    // 📌 RELACIONES

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function rivalLiga()
    {
        return $this->belongsTo(RivalLiga::class, 'rival_liga_id');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_match', 'match_id', 'player_id')
                    ->withPivot('convocado', 'valoracion')
                    ->withTimestamps();
    }

    // 📌 Método Scope para convocados
    public function scopeConvocados($query)
    {
        return $query->whereHas('players', function ($q) {
            $q->wherePivot('convocado', true);
        });
    }
}
