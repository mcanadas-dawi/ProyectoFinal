<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RivalLiga;
use App\Models\Matches;
use App\Models\Team;
use Illuminate\Support\Facades\Log;

class RivalesLigaController extends Controller
{
    // Mostrar la vista de creación de liga
    public function create()
{
    $teamId = session('current_team_id');

    if (!$teamId) {
        return redirect()->route('dashboard')->with('error', 'Selecciona una plantilla primero.');
    }

    $hayLiga = Matches::where('team_id', $teamId)
                      ->where('tipo', 'liga')
                      ->exists();

    if ($hayLiga) {
        return redirect()->route('teams.show', ['team' => $teamId])
                         ->with('error', 'Esta plantilla ya tiene una liga.');
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
        'rivales.*' => ['required', 'string', 'not_in:'],
        'solo_ida' => 'nullable|boolean',
        'team_id' => 'required|exists:teams,id', 
    ]);

    $nombreLiga = $request->input('nombre_liga');
    $rivales = $request->input('rivales');
    $soloIda = $request->boolean('solo_ida');
    $teamId = $request->input('team_id'); 
    $local = $request->input('local', []); 

    $jornada = 1;
    $local = $request->input('local', []);

    // Crear partidos de ida
    foreach ($rivales as $i => $rivalNombre) {
        if (empty($rivalNombre)) continue; // Saltar entradas vacías

        $rival = RivalLiga::create([
            'nombre_equipo' => $rivalNombre,
            'jornada' => $jornada,
            'team_id' => $teamId,
        ]);

        // Asegurarse de que los índices sean strings tal como vienen del formulario
        $esLocal = array_key_exists((string)$i, $local);
        
        Log::debug("Guardando partido: Jornada $jornada: Rival $rivalNombre, Local: " . ($esLocal ? 'Sí' : 'No'));

        Matches::create([
            'team_id' => $teamId,
            'tipo' => 'liga',
            'rival_liga_id' => $rival->id,
            'equipo_rival' => $rivalNombre,
            'fecha_partido' => now()->addDays(7 * ($jornada - 1))->toDateString(),
            'local' => $esLocal, // Asegurarse que esto se guarde como booleano
        ]);

        $jornada++;
    }
    
        // Crear partidos de vuelta (invertir local/visitante)
        if (!$soloIda) {
            foreach ($rivales as $i => $rivalNombre) {
                $rivalVuelta = RivalLiga::create([
                    'nombre_equipo' => $rivalNombre,
                    'jornada' => $jornada,
                    'team_id' => $teamId,
                ]);
    
                // En la vuelta, invertir el valor de local
                $esLocal = isset($local[$i]);
    
                Matches::create([
                    'team_id' => $teamId,
                    'tipo' => 'liga',
                    'rival_liga_id' => $rivalVuelta->id,
                    'equipo_rival' => $rivalNombre,
                    'fecha_partido' => now()->addDays(7 * ($jornada - 1))->toDateString(),
                    'local' => !$esLocal, // Invertir el valor para la vuelta
                ]);
    
                $jornada++;
            }
        }
    
        return redirect()->route('teams.show', ['team' => $teamId])
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

    public function destroyLiga($id)
    {
        $team = Team::findOrFail($id);
    
        // Elimina partidos tipo 'liga' de ese equipo
        Matches::where('team_id', $team->id)
            ->where('tipo', 'liga')
            ->delete();
    
        // Elimina los rivales asociados
        RivalLiga::where('team_id', $team->id)->delete();
    
        return redirect()->back()->with('success_liga', 'La liga y todos sus partidos han sido eliminados correctamente.');
    }
    

}
