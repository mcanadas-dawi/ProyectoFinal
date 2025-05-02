<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Facades\Log;

class PlayersController extends Controller
{
    public function store(Request $request)
    {
        Log::info('🔍 Iniciando la creación de jugador', $request->all());
    
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|max:50|unique:players,dni',
            'dorsal' => 'required|integer|min:1|max:99',
            'fecha_nacimiento' => 'required|date',
            'posicion' => 'required|string|in:Portero,Defensa,Centrocampista,Delantero',
            'perfil' => 'required|string|max:50',
        ]);
    
        try {
            // Verificar si el dorsal ya está ocupado en el equipo
            $team = Team::find($request->team_id);
            Log::info('✅ Equipo encontrado', ['team_id' => $team->id]);
    
            if ($team->players()->where('dorsal', $request->dorsal)->exists()) {
                Log::warning('❌ Dorsal ya ocupado en el equipo');
                session()->flash('error', 'Este dorsal ya está ocupado en el equipo.');
                return redirect()->back();
            }
    
            // Crear el jugador utilizando save()
            $player = new Player();
            $player->nombre = $request->nombre;
            $player->apellido = $request->apellido;
            $player->dni = $request->dni;
            $player->dorsal = $request->dorsal;
            $player->fecha_nacimiento = $request->fecha_nacimiento;
            $player->posicion = $request->posicion;
            $player->perfil = $request->perfil;
            $player->minutos_jugados = 0;
            $player->goles = 0;
            $player->asistencias = 0;
            $player->goles_encajados = 0;
            $player->titular = 0;
            $player->suplente = 0;
            $player->valoracion = 0.0;
            $player->tarjetas_amarillas = 0;
            $player->tarjetas_rojas = 0;
    
            if (!$player->save()) {
                Log::error('❌ Error al guardar el jugador utilizando save().');
                return redirect()->back()->with('error', 'Error al crear el jugador.');
            }
    
            Log::info('✅ Jugador creado correctamente', ['id' => $player->id]);
    
            // Asociar el jugador al equipo en la tabla intermedia
            try {
                $team->players()->attach($player->id);
                Log::info('✅ Jugador asociado a la plantilla', ['team_id' => $request->team_id, 'player_id' => $player->id]);
            } catch (\Exception $e) {
                Log::error('❌ Error al asociar el jugador a la plantilla: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Error al asociar el jugador a la plantilla.');
            }
    
            // Mensaje de éxito
            session()->flash('created_player', 'Jugador añadido correctamente.');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('❌ Error al crear el jugador: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el jugador.');
        }
    }
    

    public function destroy($id)
{
    try {
        // Buscar el jugador
        $player = Player::findOrFail($id);

        // Obtener el equipo asociado desde la solicitud
        $teamId = request()->input('team_id');

        // Verificar si el jugador está asociado a la plantilla actual
        $team = Team::findOrFail($teamId);

        // Eliminar la relación entre el jugador y la plantilla
        $team->players()->detach($player->id);

        // Mensaje de éxito
        session()->flash('deleted_player', 'Jugador eliminado de la plantilla correctamente');
        return redirect()->back();
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error al eliminar el jugador de la plantilla: ' . $e->getMessage());
    }
}


    public function update(Request $request, $id)
{
    try {
        $player = Player::findOrFail($id);

        $request->validate([
            'posicion' => 'required|string|in:Portero,Defensa,Centrocampista,Delantero',
            'perfil' => 'required|string|max:50',
        ]);

        // Actualizar solo los campos editables
        $player->posicion = $request->posicion;
        $player->perfil = $request->perfil;
        $player->save();

        // Enviar el mensaje de éxito como flash
        session()->flash('updated_player', 'Jugador actualizado correctamente');
        return response()->json(['success' => true, 'message' => 'Jugador actualizado correctamente']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error al actualizar el jugador: ' . $e->getMessage()], 500);
    }
}

    
    

public function addPlayerToTeam(Request $request)
{
    $request->validate([
        'player_ids' => 'required|array',
        'player_ids.*' => 'exists:players,id',
        'team_id' => 'required|exists:teams,id',
    ]);

    $team = Team::findOrFail($request->team_id);
    $added = 0;

    foreach ($request->player_ids as $playerId) {
        $player = Player::findOrFail($playerId);

        if (!$team->players()->where('players.id', $player->id)->exists()) {
            $team->players()->attach($player->id);
            $added++;
        }
    }

    if ($added > 0) {
        session()->flash('added_player', "$added jugador(es) añadido(s) correctamente a la plantilla.");
    } else {
        session()->flash('added_player', "Todos los jugadores seleccionados ya estaban en la plantilla.");
    }

    return redirect()->back();
}


}
