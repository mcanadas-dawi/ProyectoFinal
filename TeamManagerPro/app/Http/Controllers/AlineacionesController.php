<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matches;
use App\Models\Team;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AlineacionesController 
{
    public function getAlineacion($matchId)
{
    try {
        $match = Matches::findOrFail($matchId);

        // Verificar si existe una imagen guardada
        if (!$match->alineacion_imagen) {
            return response()->json([
                'success' => false,
                'message' => 'No hay alineación guardada para este partido.',
            ]);
        }

        // Verificar si el archivo existe físicamente
        if (!Storage::disk('public')->exists($match->alineacion_imagen)) {
            Log::warning("El archivo de alineación no existe en el disco: {$match->alineacion_imagen}");
            return response()->json([
                'success' => false,
                'message' => 'El archivo de alineación no se encuentra en el servidor.',
            ]);
        }

        return response()->json([
            'success' => true,
            'ruta' => $match->alineacion_imagen,
            'url' => asset('storage/' . $match->alineacion_imagen) // URL completa
        ]);

    } catch (\Exception $e) {
        Log::error("Error al obtener alineación: " . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    public function guardarAlineacion(Request $request, $matchId)
    {
        try {
            $request->validate([
                'imagen' => 'required|string', // Base64 de la imagen
            ]);
    
            $match = Matches::findOrFail($matchId);
    
            // Decodificar la imagen base64
            $imagenData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $request->imagen));
            
            // Si la decodificación falla, notificar al cliente
            if ($imagenData === false) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error al decodificar la imagen'
                ], 400);
            }
    
            // Generar un nombre único para la imagen
            $nombreArchivo = "alineacion_{$matchId}_" . time() . ".png";
    
            // Guardar la imagen en el sistema de archivos
            $rutaImagen = "alineaciones/{$nombreArchivo}";
            Storage::disk('public')->put($rutaImagen, $imagenData);
    
            // Si ya existía una imagen, eliminarla para no ocupar espacio innecesario
            if ($match->alineacion_imagen && $match->alineacion_imagen != $rutaImagen) {
                Storage::disk('public')->delete($match->alineacion_imagen);
            }
    
            // Actualizar la columna `alineacion_imagen` en la base de datos
            $match->alineacion_imagen = $rutaImagen;
            $match->save();
    
            return response()->json([
                'success' => true, 
                'ruta' => $rutaImagen,
                'url' => asset('storage/' . $rutaImagen) // URL completa para fácil referencia
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error al guardar alineación: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al guardar la alineación: ' . $e->getMessage()
            ], 500);
        }
    }
}
