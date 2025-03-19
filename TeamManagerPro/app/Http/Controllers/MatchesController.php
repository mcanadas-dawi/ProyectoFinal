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
            'equipo_rival' => 'required_if:tipo,amistoso|string',
            'fecha_partido' => 'required|date',
        ]);
    
        Matches::create([
            'team_id' => $request->team_id,
            'tipo' => $request->tipo,
            'equipo_rival' => $request->equipo_rival, // Solo aplica para amistosos
            'fecha_partido' => $request->fecha_partido,
        ]);    

    // Definir mensaje de éxito según el tipo de partido
    $message = $request->tipo === 'liga' 
        ? 'Partido de liga añadido correctamente.' 
        : 'Partido amistoso añadido correctamente.';

        return redirect()->route('teams.show', ['id' => $request->team_id])->with('success', $message);
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

    return redirect()->route('teams.show', ['id' => $match->team_id])->with('success', 'Partido actualizado correctamente.');
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
