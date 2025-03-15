<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchPlayerStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'player_id',
        'titular',
        'minutos_jugados',
        'goles',
        'asistencias',
        'tarjetas_amarillas',
        'tarjetas_rojas'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function match()
    {
        return $this->belongsTo(Matches::class);
    }
}
