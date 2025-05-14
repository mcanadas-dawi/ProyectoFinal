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

        return view('dashboard.team_show', compact('team', 'partidosLiga', 'partidosAmistosos'));
    }

    public function store(Request $request)
{

    $request->validate([
        'team_id' => 'required|exists:teams,id',
        'tipo' => 'required|in:amistoso,liga',
        'equipo_rival' => 'required_if:tipo,amistoso|string|max:255',
        'rival_liga_id' => 'nullable|integer',
        'fecha_partido' => 'required|date',
        'resultado' => 'nullable|string|max:50',
        'goles_a_favor' => 'nullable|integer',
        'goles_en_contra' => 'nullable|integer',
        'actuacion_equipo' => 'nullable|numeric', 
        'local' => 'nullable|boolean', 
    ]);

    try {
        // Verificar si el equipo existe
        $team = Team::find($request->team_id);
        if (!$team) {
            Log::error('❌ El equipo no existe.');
            return back()->with('error', 'El equipo no existe.');
        }

        // Crear el partido con todos los campos disponibles
        $matchData = [
            'team_id' => $request->team_id,
            'tipo' => $request->tipo,
            'equipo_rival' => $request->equipo_rival,
            'fecha_partido' => $request->fecha_partido,
            'resultado' => $request->resultado ?? 'Pendiente',
            'goles_a_favor' => $request->goles_a_favor ?? 0,
            'goles_en_contra' => $request->goles_en_contra ?? 0,
            'actuacion_equipo' => $request->actuacion_equipo ?? 0.0,
            'local' => $request->has('local') ? (bool) $request->local : true, // true por defecto si no viene
        ];

        // Solo añadir rival_liga_id si está presente
        if ($request->has('rival_liga_id') && $request->rival_liga_id !== null) {
            $matchData['rival_liga_id'] = $request->rival_liga_id;
        }

        // Crear el partido
        $match = Matches::create($matchData);

        if (!$match) {
            Log::error('❌ El partido no se ha creado.');
            return redirect()->back()->with('error', 'Error al crear el partido.');
        }

        // Mensaje de éxito según el tipo de partido
        $key = $request->tipo === 'liga' ? 'success_liga' : 'success_amistoso';
        $message = $request->tipo === 'liga' 
            ? 'Partido de liga añadido correctamente.' 
            : 'Partido amistoso añadido correctamente.';

        session()->flash('created_match', 'Partido amistoso creado correctamente.');

    return redirect()->back()->with($key, $message);
    } catch (\Exception $e) {
        Log::error('❌ Error al crear el partido: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error al crear el partido.');
    }
}

    


public function update(Request $request, $id)
{
    $request->validate([
        'tipo' => 'required|in:amistoso,liga',
        'rival_liga_id' => 'nullable|integer',
        'fecha_partido' => 'required|date',
        'goles_a_favor' => 'nullable|integer',
        'goles_en_contra' => 'nullable|integer',
        'resultado' => 'nullable|string|max:50',
        'actuacion_equipo' => 'nullable|numeric',
    ]);

    $match = Matches::findOrFail($id);

    // Calcular el resultado
    $resultado = 'Derrota';
    if ($request->goles_a_favor > $request->goles_en_contra) {
        $resultado = 'Victoria';
    } elseif ($request->goles_a_favor == $request->goles_en_contra) {
        $resultado = 'Empate';
    }

    // Actualizar el partido
    $match->tipo = $request->tipo;
    $match->fecha_partido = $request->fecha_partido;
    $match->goles_a_favor = $request->filled('goles_a_favor') ? intval($request->goles_a_favor) : null;
    $match->goles_en_contra = $request->filled('goles_en_contra') ? intval($request->goles_en_contra) : null;
    $match->resultado = $resultado;
    $match->actuacion_equipo = $request->filled('actuacion_equipo') ? floatval($request->actuacion_equipo) : null;
    $match->save();

    $message = $match->tipo === 'liga'
        ? 'Partido de liga actualizado correctamente'
        : 'Partido amistoso actualizado correctamente';

    $key = $match->tipo === 'liga' ? 'success_liga' : 'success_amistoso';

    session()->flash($key, $message);

    return response()->json(['success' => true, 'message' => $message], 200);
}

public function destroy($id)
{
    $match = Matches::findOrFail($id);

    // Eliminar convocatorias asociadas al partido (si existe relación en player_match)
    $match->players()->detach();

    // Eliminar el partido
    $match->delete();
    
    session()->flash('deleted_match', 'Partido amistoso eliminado correctamente.');

    return redirect()->back();
}

}
