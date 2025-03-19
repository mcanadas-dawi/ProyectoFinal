<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Team;

class PlayersController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dorsal' => 'required|integer|min:1|max:99',
            'posicion' => 'required|string|max:50',
            'perfil' => 'required|string|max:50',
        ]);

        // Verificar si el dorsal ya está ocupado en el equipo
        if (Player::where('team_id', $request->team_id)->where('dorsal', $request->dorsal)->exists()) {
            return back()->with('error', 'Este dorsal ya está ocupado en el equipo.');
        }

        Player::create($request->all());

        return back()->with('success', 'Jugador añadido correctamente.');
    }

    public function destroy($id)
    {
        $player = Player::findOrFail($id);

        // Verificar si el jugador está en algún equipo
        if ($player->teams()->exists()) {
            return back()->with('error', 'No puedes eliminar un jugador que está en un equipo.');
        }

        $player->delete();

        return back()->with('success', 'Jugador eliminado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $player = Player::findOrFail($id);

        $request->validate([
            'dorsal' => 'required|integer|min:1|max:99',
            'posicion' => 'required|string|max:50',
            'perfil' => 'required|string|max:50',
        ]);

        // Verificar si el dorsal ya está ocupado en el equipo (excepto por el mismo jugador)
        if (Player::where('team_id', $player->team_id)
                  ->where('dorsal', $request->dorsal)
                  ->where('id', '!=', $player->id)
                  ->exists()) {
            return back()->with('error', 'Este dorsal ya está ocupado en el equipo.');
        }

        $player->update($request->all());

        return back()->with('success', 'Jugador actualizado correctamente.');
    }

    public function addPlayerToTeam(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        $team = Team::findOrFail($request->team_id);
        $player = Player::findOrFail($request->player_id);

        // Agregar el jugador a la plantilla si no está ya en ella
        if (!$team->players()->where('players.id', $player->id)->exists()) {
            $team->players()->attach($player->id);
            return redirect()->back()->with('success', 'Jugador agregado correctamente a la plantilla.');
        } else {
            return redirect()->back()->with('error', 'El jugador ya pertenece a esta plantilla.');
        }
    }
}
