<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RivalLiga;
use App\Models\Matches;

class RivalesLigaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre_equipo' => 'required|string|unique:rivales_liga,nombre_equipo',
            'jornada' => 'required|integer|min:1',
            'team_id' => 'required|exists:teams,id',
        ]);

        // Verificar si ya existe un partido en esa jornada para el equipo
        if (Matches::where('team_id', $request->team_id)
                    ->whereHas('rivalLiga', function ($query) use ($request) {
                        $query->where('jornada', $request->jornada);
                    })->exists()) {
            return back()->with('error', 'Ya existe un partido en esa jornada para este equipo.');
        }

        // Crear rival en la tabla rivales_liga
        $rival = RivalLiga::create([
            'nombre_equipo' => $request->nombre_equipo,
            'jornada' => $request->jornada,
        ]);

        // Crear automáticamente el partido de liga
        Matches::create([
            'team_id' => $request->team_id,
            'tipo' => 'liga',
            'rival_liga_id' => $rival->id,
            'equipo_rival' => $rival->nombre_equipo,
            'fecha_partido' => now()->addDays(7), // Fecha por defecto en 7 días (editable después)
        ]);

        return redirect()->route('dashboard')->with('success', 'Calendario de Liga creado y partido añadido correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_equipo' => 'required|string|unique:rivales_liga,nombre_equipo,' . $id,
            'jornada' => 'required|integer|min:1',
        ]);

        $rival = RivalLiga::findOrFail($id);
        $rival->update([
            'nombre_equipo' => $request->nombre_equipo,
            'jornada' => $request->jornada,
        ]);

        return back()->with('success', 'Rival de liga actualizado correctamente.');
    }

    public function destroy($id)
    {
        $rival = RivalLiga::findOrFail($id);

        // Eliminar partidos asociados a este rival
        Matches::where('rival_liga_id', $rival->id)->delete();

        // Eliminar el rival de liga
        $rival->delete();

        return back()->with('success', 'Rival y partidos asociados eliminados correctamente.');
    }
}

