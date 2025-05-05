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
        'tarjetas_rojas',
        'valoracion'
    ];

    protected $casts = [
        'titular' => 'integer',
        'minutos_jugados' => 'integer',
        'goles' => 'integer',
        'asistencias' => 'integer',
        'tarjetas_amarillas' => 'integer',
        'tarjetas_rojas' => 'integer',
        'valoracion' => 'decimal:2'
    ];

    // 📌 RELACIONES

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function match()
    {
        return $this->belongsTo(Matches::class);
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
