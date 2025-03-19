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
}
