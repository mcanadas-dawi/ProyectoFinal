<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matches;
use App\Models\Team;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AlineacionesController extends Controller
{
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

public function guardarImagen(Request $request)
{
    $imagenData = $request->input('imagen');

    // Eliminar el encabezado 'data:image/png;base64,'
    $imagenData = preg_replace('/^data:image\/\w+;base64,/', '', $imagenData);
    $imagenData = base64_decode($imagenData);

    $nombreArchivo = 'alineacion_' . time() . '.png';

    Storage::disk('public')->put('alineaciones/' . $nombreArchivo, $imagenData);

    return response()->json(['success' => true, 'archivo' => $nombreArchivo]);
}
}
