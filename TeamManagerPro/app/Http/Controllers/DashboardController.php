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

    public function updatePlayer(Request $request, $id) // ACTUALIZAR JUGADOR
{
    $player = Player::findOrFail($id);

    $request->validate([
        'posicion' => 'nullable|in:Portero,Defensa,Centrocampista,Delantero',
        'perfil' => 'nullable|in:Diestro,Zurdo',
        'minutos_jugados' => 'nullable|integer|min:0',
        'goles' => 'nullable|integer|min:0',
        'asistencias' => 'nullable|integer|min:0',
        'titular' => 'nullable|integer|min:0',
        'suplente' => 'nullable|integer|min:0',
        'valoracion' => 'nullable|numeric|min:0|max:10',
    ]);

    // Filtrar los datos para no sobrescribir con null
    $data = array_filter($request->all(), function ($value) {
        return !is_null($value);
    });

    // Actualizar solo los datos que se enviaron
    $player->update($data);

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

public function destroyPlayer($id)
{
    // Verificar que el jugador pertenece a un equipo del usuario autenticado
    $player = Player::where('id', $id)->whereHas('teams', function ($query) {
        $query->where('user_id', Auth::id());
    })->firstOrFail();

    // Eliminar relaciones con equipos antes de eliminar el jugador
    $player->teams()->detach();

    // Finalmente, eliminar el jugador
    $player->delete();

    return back()->with('success', 'Jugador eliminado correctamente.');
}


    //PARTIDOS
    public function storeMatch(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'numero_jornada' => 'required|integer|min:1',
            'equipo_rival' => 'required|string|max:255',
            'fecha_partido' => 'required|date',
        ]);

        $team = Team::where('id', $request->team_id)->where('user_id', Auth::id())->firstOrFail();

        $match = Matches::create([
            'team_id' => $team->id,
            'numero_jornada' => $request->numero_jornada,
            'equipo_rival' => $request->equipo_rival,
            'fecha_partido' => $request->fecha_partido,
        ]);

        return redirect()->route('teams.show', $team->id)->with('success', 'Partido añadido correctamente.');
    }



public function updateMatch(Request $request, $id) 
    {
        $match = Matches::findOrFail($id);

        $request->validate([
            'fecha_partido' => 'nullable|date',
            'goles_a_favor' => 'nullable|integer|min:0',
            'goles_en_contra' => 'nullable|integer|min:0',
        ]);

        $data = array_filter($request->all(), fn($value) => !is_null($value));
        $match->update($data);

        return redirect()->back()->with('success', 'Partido actualizado con éxito.');
    }


    public function destroyMatch($id)
    {
        $match = Matches::where('id', $id)->whereHas('team', function ($query) {
            $query->where('user_id', Auth::id());
        })->firstOrFail();

        $match->delete();

        return back()->with('success', 'Partido eliminado correctamente.');
    }

    public function show($id)
    {
        $team = Team::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('players', 'matches')
            ->firstOrFail();
    
        // Obtener jugadores del usuario autenticado que NO estén en el equipo actual
        $allPlayers = Player::whereHas('teams', function ($query) {
            $query->where('teams.user_id', Auth::id());
        })->whereDoesntHave('teams', function ($query) use ($team) {
            $query->where('teams.id', $team->id);
        })->get();
    
        // Obtener jugadores convocados por partido
        $convocados = [];
        if ($team->matches->isNotEmpty()) {
            foreach ($team->matches as $match) {
                $convocados[$match->id] = $match->players()->pluck('players.id')->toArray();
            }
        }
    
        return view('dashboard.team_show', compact('team', 'allPlayers', 'convocados'));
    }
    
    
    
// Vista para valorar jugadores
public function ratePlayers($matchId) {
    $match = Matches::with('players')->findOrFail($matchId);
    return view('matches.rate_players', compact('match'));
}


public function saveRatings(Request $request, $matchId) 
{
    Log::info('Iniciando guardado de valoraciones', ['match_id' => $matchId, 'ratings' => $request->ratings]);

    $match = Matches::whereHas('team', function ($query) {
        $query->where('user_id', Auth::id());
    })->findOrFail($matchId);
    
    if ($request->has('ratings')) {
        foreach ($request->ratings as $playerId => $rating) {
            Log::info('Procesando jugador', ['player_id' => $playerId, 'rating' => $rating]);
            
            if (!is_null($rating)) {
                $match->players()->syncWithoutDetaching([$playerId => ['valoracion' => $rating]]);
                Log::info('Valoración actualizada', ['player_id' => $playerId, 'valoracion' => $rating]);
            } else {
                Log::warning('Valor nulo recibido para jugador', ['player_id' => $playerId]);
            }
        }
    } else {
        Log::error('No se recibieron valoraciones en la solicitud');
    }

    return redirect()->route('teams.show', $match->team_id)->with('success', 'Valoraciones guardadas correctamente.');
}

public function storeConvocatoria(Request $request) 
    {
        $request->validate([
            'match_id' => 'required|exists:matches,id',
            'players' => 'nullable|array',
            'players.*' => 'exists:players,id',
        ]);

        $match = Matches::whereHas('team', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($request->match_id);
        
        Log::info('Guardando convocatoria', ['match_id' => $match->id, 'players' => $request->players]);
        
        $match->players()->sync($request->players ?? []); 

        return back()->with('success', 'Convocatoria guardada correctamente.');
    }

public function updateConvocatoria(Request $request, $matchId)
{
    $match = Matches::findOrFail($matchId);

    // Obtener IDs de jugadores seleccionados
    $convocados = $request->input('convocados', []);

    // Sincronizar los jugadores convocados
    $match->players()->sync($convocados);

    return response()->json(['success' => true]);
}
}