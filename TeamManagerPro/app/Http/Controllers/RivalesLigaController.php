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
        $teamId = session('current_team_id');
        if (!$teamId) {
            return redirect()->route('dashboard')->with('error', 'Selecciona una plantilla primero.');
        }
        $team = Team::findOrFail($teamId);
        
        return view('teams.league_form', compact('team'));
    }
       
  
    // Guardar la liga con rivales y jornadas
    public function store(Request $request)
{
    $request->validate([
        'nombre_liga' => 'required|string|max:255',
        'rivales' => 'required|array',
        'rivales.*' => 'required|string',
        'solo_ida' => 'nullable|boolean',
        'team_id' => 'required|exists:teams,id', // añade validación de existencia
    ]);

    $nombreLiga = $request->input('nombre_liga');
    $rivales = $request->input('rivales');
    $soloIda = $request->has('solo_ida');
    $teamId = $request->input('team_id'); // 👈 Aquí lo obtienes claramente del formulario

    $jornada = 1;

    foreach ($rivales as $rivalNombre) {
        $rival = RivalLiga::create([
            'nombre_equipo' => $rivalNombre,
            'jornada' => $jornada,
        ]);

        Matches::create([
            'team_id' => $teamId,
            'tipo' => 'liga',
            'rival_liga_id' => $rival->id,
            'equipo_rival' => $rivalNombre,
            'fecha_partido' => now()->addDays(7 * ($jornada - 1))->toDateString(),
            'local' => true,
        ]);

        $jornada++;
    }

    if (!$soloIda) {
        foreach ($rivales as $rivalNombre) {
            $rivalVuelta = RivalLiga::create([
                'nombre_equipo' => $rivalNombre,
                'jornada' => $jornada,
            ]);

            Matches::create([
                'team_id' => $teamId,
                'tipo' => 'liga',
                'rival_liga_id' => $rivalVuelta->id,
                'equipo_rival' => $rivalNombre,
                'fecha_partido' => now()->addDays(7 * ($jornada - 1))->toDateString(),
                'local' => false,
            ]);

            $jornada++;
        }
    }

    return redirect()->route('teams.show', ['id' => $teamId])
    ->with('success', 'Liga creada correctamente.');
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
    $team = Team::findOrFail($id);

    // Eliminar partidos de liga asociados claramente
    Matches::where('team_id', $team->id)->where('tipo', 'liga')->delete();

    // Eliminar los rivales relacionados claramente
    RivalLiga::where('team_id', $team->id)->delete();

    return redirect()->back()->with('success', 'Liga eliminada correctamente.');
}

}
