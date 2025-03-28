<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RivalLiga;
use App\Models\Matches;
use App\Models\Team;

class RivalesLigaController extends Controller
{
    // Mostrar la vista de creación de liga
    public function create()
    {
        return view('ligas.create');
    }

    // Guardar la liga con rivales y jornadas
    public function store(Request $request)
    {
        $request->validate([
            'nombre_liga' => 'required|string|max:255',
            'rivales' => 'required|array',
            'rivales.*' => 'required|string',
            'solo_ida' => 'nullable|boolean',
        ]);

        $nombreLiga = $request->input('nombre_liga');
        $rivales = $request->input('rivales');
        $soloIda = $request->has('solo_ida');
        $numJornadas = $soloIda ? count($rivales) : count($rivales) * 2;

        $jornada = 1;
        foreach ($rivales as $index => $rivalNombre) {
            // Crear el rival de la primera vuelta
            $rival = RivalLiga::create([
                'nombre_equipo' => $rivalNombre,
                'jornada' => $jornada,
            ]);

            // Crear el partido correspondiente a la primera vuelta
            Matches::create([
                'team_id' => 1, // ID del equipo actual (reemplazar con el correcto)
                'tipo' => 'liga',
                'rival_liga_id' => $rival->id,
                'equipo_rival' => $rival->nombre_equipo,
                'fecha_partido' => now()->addDays(7 * $jornada),
            ]);

            // Si no es solo ida, creamos la vuelta
            if (!$soloIda) {
                $jornada++;

                // Crear el rival de la segunda vuelta (cambiando el local/visitante)
                $rivalVuelta = RivalLiga::create([
                    'nombre_equipo' => $rivalNombre,
                    'jornada' => $jornada,
                ]);

                // Crear el partido correspondiente a la segunda vuelta
                Matches::create([
                    'team_id' => 1, // ID del equipo actual (reemplazar con el correcto)
                    'tipo' => 'liga',
                    'rival_liga_id' => $rivalVuelta->id,
                    'equipo_rival' => $rivalNombre,
                    'fecha_partido' => now()->addDays(7 * $jornada),
                ]);
            }

            $jornada++;
        }

        return redirect()->route('dashboard')->with('success', 'Liga creada correctamente con todos los partidos.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_equipo' => 'required|string|max:255',
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
