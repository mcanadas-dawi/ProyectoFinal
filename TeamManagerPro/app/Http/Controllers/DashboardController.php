<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\Player;
use App\Models\Matches;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $teams = Team::where('user_id', Auth::id())->with(['players', 'matches'])->get();
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
    public function storePlayer(Request $request) //GUARDAR JUGADOR
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|unique:players,dni|max:20',
            'dorsal' => 'required|integer',
            'fecha_nacimiento' => 'required|date',
            'posicion' => 'required|in:Portero,Defensa,Centrocampista,Delantero',
            'perfil' => 'required|in:Diestro,Zurdo',
            'minutos_jugados' => 'nullable|integer|min:0',
            'goles' => 'nullable|integer|min:0',
            'asistencias' => 'nullable|integer|min:0',
            'titular' => 'nullable|integer|min:0',
            'suplente' => 'nullable|integer|min:0',
            'valoracion' => 'nullable|numeric|min:0|max:10',
            'team_id' => 'required|exists:teams,id'
        ]);

        $player = Player::create($request->except('team_id'));
        $team = Team::find($request->team_id);
        $team->players()->attach($player->id);
        return back()->with('success', 'Jugador añadido.');
    }

        public function updatePlayer(Request $request, $id) //ACTUALIZAR JUGADOR
    {
        $player = Player::findOrFail($id);

        $request->validate([
            'posicion' => 'required|in:Portero,Defensa,Centrocampista,Delantero',
            'perfil' => 'required|in:Diestro,Zurdo',
            'minutos_jugados' => 'nullable|integer|min:0',
            'goles' => 'nullable|integer|min:0',
            'asistencias' => 'nullable|integer|min:0',
            'titular' => 'nullable|integer|min:0',
            'suplente' => 'nullable|integer|min:0',
            'valoracion' => 'nullable|numeric|min:0|max:10',
        ]);

        $player->update([
            'posicion' => $request->posicion,
            'perfil' => $request->perfil,
            'minutos_jugados' => $request->minutos_jugados,
            'goles' => $request->goles,
            'asistencias' => $request->asistencias,
            'titular' => $request->titular,
            'suplente' => $request->suplente,
            'valoracion' => $request->valoracion,

        ]);

        return redirect()->back()->with('success', 'Jugador actualizado con éxito.');
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

    public function destroyPlayer($id) //ELIMINAR JUGADOR
    {
        Player::findOrFail($id)->delete();
        return back()->with('success', 'Jugador eliminado correctamente.');
    }

    //PARTIDOS
    public function storeMatch(Request $request) //GUARDAR PARTIDO
{
    $request->validate([
        'team_id' => 'required|exists:teams,id',
        'numero_jornada' => 'required|integer|min:1|unique:matches,numero_jornada,NULL,id,team_id,' . $request->team_id,
        'equipo_rival' => 'required|string|max:255',
        'fecha_partido' => 'required|date',
        'goles_a_favor' => 'required|integer|min:0',
        'goles_en_contra' => 'required|integer|min:0',
    ]);

    Matches::create($request->all());

    return back()->with('success', 'Partido añadido con éxito.');
}


public function updateMatch(Request $request, $id) //ACTUALIZAR PARTIDO
{
    $match = Matches::findOrFail($id);

    $request->validate([
        'fecha_partido' => 'required|date',
        'goles_a_favor' => 'required|integer|min:0',
        'goles_en_contra' => 'required|integer|min:0',
    ]);

    $match->update([
        'fecha_partido' => $request->fecha_partido,
        'goles_a_favor' => $request->goles_a_favor,
        'goles_en_contra' => $request->goles_en_contra,
    ]);

    return redirect()->back()->with('success', 'Partido actualizado con éxito.');
}


public function destroyMatch($id) //ELIMINAR PARTIDO
{
    Matches::findOrFail($id)->delete();
    return back()->with('success', 'Partido eliminado correctamente.');
}

public function show($id)
{
    $team = Team::with('players', 'matches')->findOrFail($id);
    $allPlayers = Player::all(); // Obtener todos los jugadores disponibles

    // Obtener los jugadores convocados por partido
    $convocados = [];
    foreach ($team->matches as $match) {
        $convocados[$match->id] = $match->players()->pluck('players.id')->toArray();
    }

    return view('dashboard.team_show', compact('team', 'allPlayers', 'convocados'));
}


// Vista para valorar jugadores
public function ratePlayers($matchId) {
    $match = Matches::with('players')->findOrFail($matchId);
    return view('matches.rate_players', compact('match'));
}

// Guardar valoraciones
public function saveRatings(Request $request, $matchId) {
    $match = Matches::findOrFail($matchId);

    foreach ($request->ratings as $playerId => $rating) {
        $match->players()->updateExistingPivot($playerId, ['valoracion' => $rating]);
    }

    return redirect()->route('dashboard')->with('success', 'Valoraciones guardadas correctamente.');
}

public function storeConvocatoria(Request $request) //CONVOCATORIA
{
    $request->validate([
        'match_id' => 'required|exists:matches,id',
        'players' => 'array',
        'players.*' => 'exists:players,id',
    ]);

    $match = Matches::findOrFail($request->match_id);
    $match->players()->sync($request->players); // Actualiza la relación entre partido y jugadores convocados

    return back()->with('success', 'Convocatoria guardada correctamente.');
}
public function updateConvocatoria(Request $request, $matchId) //ACTUALIZAR CONVOCATORIA
{
    $match = Matches::findOrFail($matchId);

    // Obtener IDs de jugadores seleccionados
    $convocados = $request->input('convocados', []);

    // Sincronizar los jugadores convocados
    $match->players()->sync($convocados);

    return response()->json(['success' => true]);
}

}