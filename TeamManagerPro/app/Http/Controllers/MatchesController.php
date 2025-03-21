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
        'actuacion_equipo' => 'nullable|string|max:255',
    ]);

    // Verificar si el equipo existe
    $team = Team::find($request->team_id);
    if (!$team) {
        return back()->with('error', 'El equipo no existe.');
    }

    // Crear el partido con todos los campos disponibles
    $match = Matches::create([
        'team_id' => $request->team_id,
        'rival_liga_id' => $request->rival_liga_id,
        'tipo' => $request->tipo,
        'equipo_rival' => $request->equipo_rival,
        'fecha_partido' => $request->fecha_partido,
        'resultado' => $request->resultado ?? 'N/A',
        'goles_a_favor' => $request->goles_a_favor ?? 0,
        'goles_en_contra' => $request->goles_en_contra ?? 0,
        'actuacion_equipo' => $request->actuacion_equipo ?? 0.0,
    ]);

    // Mensaje de éxito según el tipo de partido
    $message = $request->tipo === 'liga' 
        ? 'Partido de liga añadido correctamente.' 
        : 'Partido amistoso añadido correctamente.';

    // Redirigir a la vista del equipo para mostrar el partido creado
    return redirect()->route('teams.show', $request->team_id)->with('success', $message);
}

    

public function update(Request $request, $id)
{
    Log::info('Datos recibidos en update:', $request->all());

    $request->validate([
        'tipo' => 'required|in:amistoso,liga',
        'equipo_rival' => 'nullable|string|max:255',
        'rival_liga_id' => 'nullable|integer',
        'fecha_partido' => 'required|date',
        'goles_a_favor' => 'nullable|integer',
        'goles_en_contra' => 'nullable|integer',
        'resultado' => 'nullable|string|max:50',
        'actuacion_equipo' => 'nullable|numeric',
    ]);

    $match = Matches::findOrFail($id);

    // Calcular el resultado explícitamente
    $resultado = 'Derrota';
    if ($request->goles_a_favor > $request->goles_en_contra) {
        $resultado = 'Victoria';
    } elseif ($request->goles_a_favor == $request->goles_en_contra) {
        $resultado = 'Empate';
    }

    Log::info("Resultado calculado: " . $resultado);

    // Actualizar el partido explícitamente
    $match->tipo = $request->tipo;
    $match->equipo_rival = $request->equipo_rival;
    $match->rival_liga_id = $request->rival_liga_id;
    $match->fecha_partido = $request->fecha_partido;
    $match->goles_a_favor = $request->goles_a_favor;
    $match->goles_en_contra = $request->goles_en_contra;
    $match->resultado = $resultado;
    $match->actuacion_equipo = $request->actuacion_equipo;

    Log::info("Valores antes de guardar:", $match->toArray());

    // Guardar el modelo de forma explícita
    if ($match->save()) {
        Log::info('Partido actualizado correctamente en la base de datos:', $match->toArray());
    } else {
        Log::error('Error al guardar el partido en la base de datos.');
    }

    return response()->json(['success' => true, 'message' => 'Partido actualizado correctamente']);
}






public function destroy($id)
{
    $match = Matches::findOrFail($id);

    // Eliminar convocatorias asociadas al partido (si existe relación en player_match)
    $match->players()->detach();

    // Eliminar el partido
    $match->delete();

    return back()->with('success', 'Partido eliminado correctamente.');
}

}
