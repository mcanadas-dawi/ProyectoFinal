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


    //PARTIDOS
    public function storeMatch(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'numero_jornada' => 'required|integer|min:1',
            'equipo_rival' => 'required|string|max:255',
            'fecha_partido' => 'required|date',
            'resultado' => 'nullable|in:Victoria,Empate,Derrota',
            'actuacion_equipo' => 'nullable|numeric|min:0|max:10',
        ]);
    
        $match = Matches::create($request->all());
    
        return redirect()->route('teams.show', $request->team_id)->with('success', 'Partido añadido correctamente.');
    }
    



    public function updateMatch(Request $request, $id) {
        $match = Matches::findOrFail($id);
    
        $request->validate([
            'fecha_partido' => 'nullable|date',
            'goles_a_favor' => 'nullable|integer|min:0',
            'goles_en_contra' => 'nullable|integer|min:0',
            'resultado' => 'nullable|in:Victoria,Empate,Derrota',
            'actuacion_equipo' => 'nullable|numeric|min:0|max:10',
        ]);
    
        $data = $request->all();
    
        // 📌 Calcular resultado automáticamente si no se ha enviado manualmente
        if (!isset($data['resultado']) || empty($data['resultado'])) {
            if ($data['goles_a_favor'] > $data['goles_en_contra']) {
                $data['resultado'] = "Victoria";
            } elseif ($data['goles_a_favor'] < $data['goles_en_contra']) {
                $data['resultado'] = "Derrota";
            } else {
                $data['resultado'] = "Empate";
            }
        }
    
        $match->update($data);
    
        return redirect()->back()->with('success', 'Partido actualizado con éxito.');
    }
    
    
    


    public function destroyMatch($id)
{
    $match = Matches::whereHas('team', function ($query) {
        $query->where('user_id', Auth::id());
    })->findOrFail($id);

    // 📌 **Obtener las estadísticas de los jugadores en el partido a eliminar**
    $playerStats = MatchPlayerStat::where('match_id', $id)->get();

    foreach ($playerStats as $stat) {
        $stats = PlayerTeamStats::where('player_id', $stat->player_id)
                                ->where('team_id', $match->team_id)
                                ->first();

        if ($stats) {
            // 📌 **Restar las estadísticas del partido eliminado**
            $stats->update([
                'minutos_jugados' => max(0, $stats->minutos_jugados - $stat->minutos_jugados),
                'goles' => max(0, $stats->goles - $stat->goles),
                'asistencias' => max(0, $stats->asistencias - $stat->asistencias),
                'tarjetas_amarillas' => max(0, $stats->tarjetas_amarillas - $stat->tarjetas_amarillas),
                'tarjetas_rojas' => max(0, $stats->tarjetas_rojas - $stat->tarjetas_rojas),
                'titular' => max(0, $stats->titular - $stat->titular),
                'suplente' => max(0, $stats->suplente - $stat->suplente),
            ]);
        }
    }

    // 📌 **Eliminar las estadísticas del partido y el partido**
    MatchPlayerStat::where('match_id', $id)->delete();
    $match->delete();

    return back()->with('success', 'Partido eliminado correctamente.');
}


    public function show($id)
{
    $team = Team::where('id', $id)
        ->where('user_id', Auth::id())
        ->with(['players', 'matches' => function ($query) {
            $query->orderBy('numero_jornada', 'asc'); // Ordenar partidos por fecha (más reciente primero)
        }])
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
            $convocados[$match->id] = $match->players()
                ->where('player_match.convocado', true)
                ->pluck('players.id')
                ->toArray();
        }
    }

    $stats = $this->getTeamStats($id);
    $latestMatch = $team->matches->first(); // Ahora realmente obtiene el partido más reciente

    return view('dashboard.team_show', compact('team', 'allPlayers', 'convocados', 'stats', 'latestMatch'));
}

    
    
    
// Vista para valorar jugadores
public function ratePlayers($matchId) {
    $match = Matches::with(['convocados'])->findOrFail($matchId);

    return view('matches.rate_players', compact('match'));
}

public function saveRatings(Request $request, $matchId) 
{
    Log::info('Guardando valoraciones', ['match_id' => $matchId, 'players' => $request->players]);

    $match = Matches::findOrFail($matchId);
    $teamId = $match->team_id; // Obtener el equipo del partido

    if ($request->has('players')) {
        foreach ($request->players as $playerId => $data) {
            $player = Player::findOrFail($playerId);

            $esTitular = isset($data['titular']) ? 1 : 0;
            $esSuplente = $esTitular ? 0 : 1;

            // 📌 **Determinar las tarjetas amarillas**
            $amarillas = $data['tarjetas_amarillas'] ?? 0;

            // 📌 **Si el jugador tiene 2 amarillas, recibe automáticamente 1 roja (NO EDITABLE)**
            if ($amarillas == 2) {
                $rojas = 1;
            } else {
                // 📌 **Si tiene 0 o 1 amarilla, la roja es editable**
                $rojas = $data['tarjetas_rojas'] ?? 0;
            }

            $valoracion = $data['valoracion'] ?? null;

            // 📌 **Obtener estadísticas del jugador en el partido**
            $existingMatchStats = MatchPlayerStat::where('match_id', $matchId)
                ->where('player_id', $playerId)
                ->first();

            // 📌 **Obtener estadísticas acumuladas en la plantilla**
            $stats = PlayerTeamStats::firstOrCreate([
                'player_id' => $playerId,
                'team_id' => $teamId
            ]);

            if ($existingMatchStats) {
                // 📌 **Restar los valores antiguos antes de actualizar**
                $stats->update([
                    'minutos_jugados' => max(0, $stats->minutos_jugados - $existingMatchStats->minutos_jugados),
                    'goles' => max(0, $stats->goles - $existingMatchStats->goles),
                    'asistencias' => max(0, $stats->asistencias - $existingMatchStats->asistencias),
                    'tarjetas_amarillas' => max(0, $stats->tarjetas_amarillas - $existingMatchStats->tarjetas_amarillas),
                    'tarjetas_rojas' => max(0, $stats->tarjetas_rojas - $existingMatchStats->tarjetas_rojas),
                    'titular' => max(0, $stats->titular - $existingMatchStats->titular),
                    'suplente' => max(0, $stats->suplente - $existingMatchStats->suplente),
                ]);

                // 📌 **Actualizar los valores en `match_player_stats`**
                $existingMatchStats->update([
                    'minutos_jugados' => $data['minutos_jugados'] ?? 0,
                    'goles' => $data['goles'] ?? 0,
                    'asistencias' => $data['asistencias'] ?? 0,
                    'tarjetas_amarillas' => $amarillas,
                    'tarjetas_rojas' => $rojas, // 📌 **Aplica la nueva lógica de tarjetas rojas**
                    'valoracion' => $valoracion,
                    'titular' => $esTitular,
                    'suplente' => $esSuplente
                ]);
            } else {
                // 📌 **Si el partido es nuevo, crear entrada y sumar a `player_team_stats`**
                MatchPlayerStat::create([
                    'match_id' => $matchId,
                    'player_id' => $playerId,
                    'minutos_jugados' => $data['minutos_jugados'] ?? 0,
                    'goles' => $data['goles'] ?? 0,
                    'asistencias' => $data['asistencias'] ?? 0,
                    'tarjetas_amarillas' => $amarillas,
                    'tarjetas_rojas' => $rojas,
                    'valoracion' => $valoracion,
                    'titular' => $esTitular,
                    'suplente' => $esSuplente
                ]);
            }

            // 📌 **Sumar los nuevos valores en `player_team_stats` después de actualizar `match_player_stats`**
            $stats->update([
                'minutos_jugados' => $stats->minutos_jugados + ($data['minutos_jugados'] ?? 0),
                'goles' => $stats->goles + ($data['goles'] ?? 0),
                'asistencias' => $stats->asistencias + ($data['asistencias'] ?? 0),
                'tarjetas_amarillas' => $stats->tarjetas_amarillas + $amarillas,
                'tarjetas_rojas' => $stats->tarjetas_rojas + $rojas,
                'titular' => $stats->titular + $esTitular,
                'suplente' => $stats->suplente + $esSuplente,
                'valoracion' => $this->calcularValoracionMedia($playerId, $teamId)
            ]);
        }
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

    if ($request->has('players')) {
        $convocados = [];
        foreach ($request->players as $playerId) {
            $convocados[$playerId] = ['convocado' => true]; // 📌 Ahora 'convocado' se almacena correctamente
        }

        $match->players()->syncWithoutDetaching($convocados);
    }

    return back()->with('success', 'Convocatoria guardada correctamente.');
}


public function updateConvocatoria(Request $request, $matchId)
{
    $match = Matches::findOrFail($matchId);

    // Obtener IDs de jugadores seleccionados
    $convocados = $request->input('convocados', []);

    // Sincronizar los jugadores convocados
    $convocadosConEstado = [];
    foreach ($convocados as $playerId) {
        $convocadosConEstado[$playerId] = ['convocado' => true];
    }

    // 📌 **Corregido: Se usa `sync()` para actualizar correctamente**
    $match->players()->sync($convocadosConEstado);

    return response()->json(['success' => true]);
}



public function saveAlineacion(Request $request, $matchId)
{
    try {
        Log::info("Guardando alineación para partido: $matchId", ['alineacion' => $request->alineacion]);

        $match = Matches::findOrFail($matchId);

        // Asegurar que la relación con jugadores está correctamente definida en el modelo Matches
        if (!$match) {
            throw new \Exception("El partido con ID $matchId no existe.");
        }

        // Validar que los datos llegan correctamente
        if (!$request->has('alineacion') || !is_array($request->alineacion)) {
            throw new \Exception("El campo 'alineacion' está vacío o mal formado.");
        }

        // Eliminar alineación anterior
        $match->players()->detach();

        // Guardar la nueva alineación
        foreach ($request->alineacion as $alineacion) {
            if (!isset($alineacion['player_id']) || !isset($alineacion['posicion'])) {
                throw new \Exception("Datos incompletos en alineación: " . json_encode($alineacion));
            }

            Log::info("Asignando jugador {$alineacion['player_id']} a la posición {$alineacion['posicion']} en el partido $matchId");

            // Verificar si el jugador existe en la base de datos antes de asignarlo
            $player = Player::find($alineacion['player_id']);
            if (!$player) {
                throw new \Exception("El jugador con ID {$alineacion['player_id']} no existe.");
            }

            // Asociar el jugador con la posición en el partido
            $match->players()->attach($alineacion['player_id'], ['posicion' => $alineacion['posicion']]);
        }

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        Log::error("Error al guardar alineación: " . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}




public function getAlineacion($matchId)
{
    try {
        $match = Matches::findOrFail($matchId);

        // Obtener la alineación del partido
        $alineacion = $match->players()->select('players.id as player_id', 'player_match.posicion')->get();

        // Obtener la formación guardada (puedes guardarla en la base de datos si aún no lo haces)
        $formacion = $match->formacion ?? 'Seleccionar...';

        return response()->json([
            'success' => true,
            'alineacion' => $alineacion,
            'formacion' => $formacion
        ]);

    } catch (\Exception $e) {
        Log::error("Error al obtener alineación: " . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

//ESTADISTICAS DE LA PLANTILLA
public function getTeamStats($teamId)
{
    $team = Team::with('matches')->findOrFail($teamId);

    // Calcular estadísticas
    $victorias = $team->matches->where('resultado', 'Victoria')->count();
    $empates = $team->matches->where('resultado', 'Empate')->count();
    $derrotas = $team->matches->where('resultado', 'Derrota')->count();
    
    $puntos = ($victorias * 3) + ($empates * 1);
    $golesFavor = $team->matches->sum('goles_a_favor');
    $golesContra = $team->matches->sum('goles_en_contra');

    // ⚠️ Asegúrate de añadir las tarjetas en la base de datos más adelante
    $tarjetasAmarillas = $team->players->sum('tarjetas_amarillas');
    $tarjetasRojas = $team->players->sum('tarjetas_rojas');
    $valoracionMedia = $team->matches->avg('actuacion_equipo');

    return [
        'victorias' => $victorias,
        'empates' => $empates,
        'derrotas' => $derrotas,
        'puntos' => $puntos,
        'goles_favor' => $golesFavor,
        'goles_contra' => $golesContra,
        'tarjetas_amarillas' => $tarjetasAmarillas,
        'tarjetas_rojas' => $tarjetasRojas,
        'valoracion_media' => round($valoracionMedia, 2)
    ];
}

private function calcularValoracionMedia($playerId, $teamId)
{
    // Obtener todas las valoraciones del jugador en la plantilla
    $valoraciones = MatchPlayerStat::whereHas('match', function ($query) use ($teamId) {
        $query->where('team_id', $teamId);
    })->where('player_id', $playerId)
      ->whereNotNull('valoracion')
      ->pluck('valoracion');

    // Si no hay valoraciones, devolver 0
    if ($valoraciones->isEmpty()) {
        return 0;
    }

    // Calcular el promedio de valoraciones
    return round($valoraciones->avg(), 2);
}

}