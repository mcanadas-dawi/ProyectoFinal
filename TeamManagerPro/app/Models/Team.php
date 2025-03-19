<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'modalidad', 'user_id'];

    // 📌 RELACIONES

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_team');
    }

    public function matches()
    {
        return $this->hasMany(Matches::class);
    }

    public function rivalesLiga()
    {
        return $this->hasMany(RivalLiga::class);
    }

    // 📌 SCOPES

    public function scopePartidosLiga($query)
    {
        return $query->whereHas('matches', function ($q) {
            $q->where('tipo', 'liga');
        });
    }

    public function scopePartidosAmistosos($query)
    {
        return $query->whereHas('matches', function ($q) {
            $q->where('tipo', 'amistoso');
        });
    }

    // 📌 MUTATORS Y ACCESSORS

    public function getNombreAttribute($value)
    {
        return ucfirst($value); // Primera letra en mayúscula
    }
}


