<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matches;
use App\Models\Team;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class MatchesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($teamId)
    {
        $team = Team::findOrFail($teamId);
        
        // Obtener partidos de liga
        $partidosLiga = Matches::where('team_id', $teamId)
                        ->where('tipo', 'liga')
                        ->with('rivalLiga')
                        ->get();

        // Obtener partidos amistosos
        $partidosAmistosos = Matches::where('team_id', $teamId)
                            ->where('tipo', 'amistoso')
                            ->get();
        
        return view('dashboard.team_show', compact('team', 'partidosLiga', 'partidosAmistosos'));
    }

    public function show($teamId)
    {
        $team = Team::findOrFail($teamId);
    
        // Obtener partidos de liga
        $partidosLiga = Matches::where('team_id', $teamId)
                        ->where('tipo', 'liga')
                        ->with('rivalLiga')
                        ->get();
    
        // Obtener partidos amistosos
        $partidosAmistosos = Matches::where('team_id', $teamId)
                            ->where('tipo', 'amistoso')
                            ->get();
    
        // Obtener jugadores convocados
        $convocados = [];
        foreach ($partidosLiga as $match) {
            $convocados[$match->id] = $match->players()->wherePivot('convocado', true)->pluck('id')->toArray();
        }
        $rivales = \App\Models\RivalLiga::all();
    
        return view('dashboard.team_show', compact('team', 'partidosLiga', 'partidosAmistosos', 'convocados', 'rivales'));
    }
    



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'team_id' => 'required|exists:teams,id',
        'tipo' => 'required|in:amistoso,liga',
        'rival_liga_id' => 'nullable|exists:rivales_liga,id',
        'equipo_rival' => 'required_if:tipo,amistoso|string',
        'fecha_partido' => 'required|date',
    ]);

    $match = Matches::create([
        'team_id' => $request->team_id,
        'tipo' => $request->tipo,
        'rival_liga_id' => $request->tipo === 'liga' ? $request->rival_liga_id : null,
        'equipo_rival' => $request->tipo === 'amistoso' ? $request->equipo_rival : null,
        'fecha_partido' => $request->fecha_partido,
    ]);

    // Definir mensaje de éxito según el tipo de partido
    $message = $request->tipo === 'liga' 
        ? 'Partido de liga añadido correctamente.' 
        : 'Partido amistoso añadido correctamente.';

    return redirect()->route('dashboard')->with('success', $message);
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

public function ratePlayers($matchId)
{
    $match = Matches::with('players')->findOrFail($matchId);

    return view('matches.rate_players', compact('match'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'fecha_partido' => 'required|date',
        'goles_a_favor' => 'nullable|integer|min:0',
        'goles_en_contra' => 'nullable|integer|min:0',
        'resultado' => 'nullable|string|in:Victoria,Empate,Derrota',
        'actuacion_equipo' => 'nullable|numeric|min:0|max:10',
    ]);

    $match = Matches::findOrFail($id);
    $match->update([
        'fecha_partido' => $request->fecha_partido,
        'goles_a_favor' => $request->goles_a_favor ?? 0,
        'goles_en_contra' => $request->goles_en_contra ?? 0,
        'resultado' => $request->resultado,
        'actuacion_equipo' => $request->actuacion_equipo ?? null,
    ]);

    return back()->with('success', 'Partido actualizado correctamente.');
}


}
