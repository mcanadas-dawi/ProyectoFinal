<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\Player;
use App\Models\Matches;
use App\Models\MatchPlayerStat;
use App\Models\PlayerTeamStats;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
{
    $teams = Team::where('user_id', Auth::id())->with('players')->get();
    return view('dashboard.index', compact('teams'));
}


    // PLANTILLAS
    public function storeTeam(Request $request) //GUARDAR PLANTILLA
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'modalidad' => 'required|in:F5,F7,F8,F11'
        ]);

        Team::create([
            'nombre' => $request->nombre,
            'modalidad' => $request->modalidad,
            'user_id' => Auth::id()
        ]);

        return back()->with('success', 'Plantilla creada con éxito.');
    }

    public function destroyTeam($id) //ELIMINAR EQUIPO
{
    Team::findOrFail($id)->delete();
    return redirect('/dashboard')->with('success', 'Equipo eliminado correctamente.');
}

    // JUGADORES
    public function storePlayer(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|unique:players,dni|max:20',
            'dorsal' => 'required|integer',
            'fecha_nacimiento' => 'required|date',
            'posicion' => 'required|in:Portero,Defensa,Centrocampista,Delantero',
            'perfil' => 'required|in:Diestro,Zurdo',
            'team_id' => 'required|exists:teams,id',
        ]);

        $team = Team::where('id', $request->team_id)->where('user_id', Auth::id())->firstOrFail();

        $player = Player::create($request->except('team_id'));
        $team->players()->attach($player->id);

        return back()->with('success', 'Jugador añadido correctamente.');
    }

    public function updatePlayer(Request $request, $id)
{
    $player = Player::findOrFail($id);

    $request->validate([
        'posicion' => 'required|in:Portero,Defensa,Centrocampista,Delantero',
        'perfil' => 'required|in:Diestro,Zurdo',
    ]);

    $player->update([
        'posicion' => $request->posicion,
        'perfil' => $request->perfil,
    ]);

    return redirect()->back()->with('success', 'Posición y perfil actualizados con éxito.');
}

    public function addPlayerToTeam(Request $request)
{
    $request->validate([
        'player_id' => 'required|exists:players,id',
        'team_id' => 'required|exists:teams,id',
    ]);

    $team = Team::findOrFail($request->team_id);
    $player = Player::findOrFail($request->player_id);

    // Agregar el jugador a la plantilla si no está ya en ella
    if (!$team->players()->where('player_id', $player->id)->exists()) {
        $team->players()->attach($player->id);
        return redirect()->back()->with('success', 'Jugador agregado correctamente a la plantilla.');
    } else {
        return redirect()->back()->with('error', 'El jugador ya pertenece a esta plantilla.');
    }
}

public function destroyPlayer(Request $request, $id)
{
    $player = Player::findOrFail($id);
    
    // Verifica que el jugador pertenece a la plantilla específica del usuario
    $team = Team::where('id', $request->team_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

    // Desvincular el jugador de la plantilla específica
    $team->players()->detach($player->id);

    // Si el jugador ya no está en ninguna plantilla, puedes eliminarlo opcionalmente
    if ($player->teams()->count() == 0) {
        $player->delete();
    }

    return back()->with('success', 'Jugador eliminado de la plantilla correctamente.');
}

public function show($id)
{
    $team = Team::where('id', $id)
        ->where('user_id', Auth::id())
        ->with('players')
        ->firstOrFail();

    $allPlayers = Player::whereNotIn('id', $team->players->pluck('id'))->get();
    $stats = $this->getTeamStats($id);

    return view('dashboard.team_show', compact('team', 'allPlayers', 'stats'));
}



//ESTADISTICAS DE LA PLANTILLA
public function getTeamStats($teamId)
{
    $team = Team::with(['matches' => function ($query) {
        $query->where('tipo', 'liga'); // Solo contar partidos de liga
    }])->findOrFail($teamId);

    $victorias = $team->matches->where('resultado', 'Victoria')->count();
    $empates = $team->matches->where('resultado', 'Empate')->count();
    $derrotas = $team->matches->where('resultado', 'Derrota')->count();

    $puntos = ($victorias * 3) + ($empates * 1);
    $golesFavor = $team->matches->sum('goles_a_favor');
    $golesContra = $team->matches->sum('goles_en_contra');

    return [
        'victorias' => $victorias,
        'empates' => $empates,
        'derrotas' => $derrotas,
        'puntos' => $puntos,
        'golesFavor' => $golesFavor,
        'golesContra' => $golesContra,
    ];
}

}