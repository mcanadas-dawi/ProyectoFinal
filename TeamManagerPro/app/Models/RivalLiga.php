<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RivalLiga extends Model
{
    use HasFactory;

    protected $table = 'rivales_liga';

    protected $fillable = [
        'nombre_equipo',
        'jornada',
        'team_id'
    ];

    // 📌 RELACIONES

    public function matches()
    {
        return $this->hasMany(Matches::class, 'rival_liga_id');
    }

    // 📌 Si cada rival de liga está asociado a un equipo, podemos agregar esta relación:
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // 📌 MUTATORS Y ACCESSORS

    public function getJornadaAttribute($value)
    {
        return $value ?? 1; // Si no hay jornada definida, asignar la 1 por defecto
    }
}

