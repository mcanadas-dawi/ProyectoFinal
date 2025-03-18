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
        'jornada'
    ];

    public function matches()
    {
        return $this->hasMany(Matches::class, 'rival_liga_id');
    }
}
