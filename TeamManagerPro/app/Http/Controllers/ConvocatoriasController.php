<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matches;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConvocatoriasController extends Controller
{
    public function store(Request $request)
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
                $convocados[$playerId] = ['convocado' => true];
            }

            $match->players()->syncWithoutDetaching($convocados);
        }

        return back()->with('success', 'Convocatoria guardada correctamente.');
    }

    public function update(Request $request, $matchId)
    {
        // Validar la solicitud
        $request->validate([
            'convocados' => 'array',
            'convocados.*' => 'exists:players,id'
        ]);
    
        // Obtener el partido
        $match = Matches::findOrFail($matchId);
    
        // Obtener los IDs de los jugadores convocados
        $convocados = $request->input('convocados', []);
    
        // Crear un array para sincronizar los convocados con el estado
        $convocadosConEstado = [];
        foreach ($convocados as $playerId) {
            $convocadosConEstado[$playerId] = ['convocado' => true];
        }
    
        // Sincronizar los jugadores convocados con el partido
        $match->players()->sync($convocadosConEstado);
        
        session()->flash('success_convocatoria', 'Convocatoria actualizada correctamente.');

        return response()->json(['success' => true]);
    }
    
    public function getConvocados($matchId)
    {
        try {
            $match = Matches::with(['players' => function ($query) {
                $query->wherePivot('convocado', true); // Filtrar solo jugadores convocados
            }])->findOrFail($matchId);
    
            $convocados = $match->players;
    
            return response()->json([
                'convocados' => $convocados,
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'convocados' => [],
                'success' => false,
                'message' => 'No hay jugadores convocados para este partido.',
            ]);
        }
    }
}