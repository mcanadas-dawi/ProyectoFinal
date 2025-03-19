<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matches;
use App\Models\Team;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AlineacionesController extends Controller
{
    public function save(Request $request, $matchId)
    {
        try {
            Log::info("Guardando alineación para partido: $matchId", ['alineacion' => $request->alineacion]);

            $match = Matches::findOrFail($matchId);

            if (!$match) {
                throw new \Exception("El partido con ID $matchId no existe.");
            }

            if (!$request->has('alineacion') || !is_array($request->alineacion)) {
                throw new \Exception("El campo 'alineacion' está vacío o mal formado.");
            }

            $match->players()->detach();

            foreach ($request->alineacion as $alineacion) {
                if (!isset($alineacion['player_id']) || !isset($alineacion['posicion'])) {
                    throw new \Exception("Datos incompletos en alineación: " . json_encode($alineacion));
                }

                Log::info("Asignando jugador {$alineacion['player_id']} a la posición {$alineacion['posicion']} en el partido $matchId");

                $match->players()->attach($alineacion['player_id'], ['posicion' => $alineacion['posicion']]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Error al guardar alineación: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
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
}
