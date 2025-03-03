<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\Player;
use App\Models\Matches;

class DashboardController extends Controller
{
    public function index()
    {
        $teams = Team::where('user_id', Auth::id())->with(['players', 'matches'])->get();
        return view('dashboard.index', compact('teams'));
    }

    public function storeTeam(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'modalidad' => 'required|in:F5,F7,F8,F11'
        ]);

        Team::create([
            'nombre' => $request->nombre,
            'modalidad' => $request->modalidad,
            'user_id' => Auth::id()
        ]);

        return back()->with('success', 'Plantilla creada con éxito.');
    }

    public function storePlayer(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dorsal' => 'required|integer',
            'fecha_nacimiento' => 'required|date',
            'posicion' => 'required|in:Portero,Defensa,Centrocampista,Delantero',
            'perfil' => 'required|in:Diestro,Zurdo',
            'team_id' => 'required|exists:teams,id'
        ]);

        $player = Player::create($request->except('team_id'));
        $team = Team::find($request->team_id);
        $team->players()->attach($player->id);
        return back()->with('success', 'Jugador añadido.');
    }

    public function storeMatch(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'numero_jornada' => 'required|integer',
            'equipo_rival' => 'required|string|max:255',
            'fecha_partido' => 'required|date',
            'resultado' => 'nullable|in:Victoria,Empate,Derrota',
            'goles_a_favor' => 'required|integer',
            'goles_en_contra' => 'required|integer',
            'actuacion_equipo' => 'nullable|numeric|min:0|max:10'
        ]);

        Matches::create($request->all());
        return back()->with('success', 'Partido añadido.');
    }

public function show($id)
{
    $team = Team::with(['players', 'matches'])->findOrFail($id);
    return view('dashboard.team_show', compact('team'));
}


public function destroyPlayer($id)
{
    Player::findOrFail($id)->delete();
    return back()->with('success', 'Jugador eliminado correctamente.');
}

public function updateAllMatches(Request $request)
{
    foreach ($request->matches as $id => $data) {
        $match = Matches::findOrFail($id);
        $match->update([
            'goles_a_favor' => $data['goles_a_favor'],
            'goles_en_contra' => $data['goles_en_contra'],
        ]);
    }

    return back()->with('success', 'Resultados de los partidos actualizados.');
}

public function destroyTeam($id)
{
    Team::findOrFail($id)->delete();
    return redirect('/dashboard')->with('success', 'Equipo eliminado correctamente.');
}

public function updatePlayer(Request $request, $id)
{
    $player = Player::findOrFail($id);

    $request->validate([
        'posicion' => 'required|in:Portero,Defensa,Centrocampista,Delantero',
        'perfil' => 'required|in:Diestro,Zurdo',
        'minutos_jugados' => 'nullable|integer|min:0',
        'goles' => 'nullable|integer|min:0',
    ]);

    $player->update($request->all());

    return response()->json(['message' => 'Jugador actualizado con éxito.']);
}

public function updateMatch(Request $request, $id)
{
    $match = Matches::findOrFail($id);

    $request->validate([
        'goles_a_favor' => 'required|integer|min:0',
        'goles_en_contra' => 'required|integer|min:0',
    ]);

    $match->update($request->all());

    return response()->json(['message' => 'Partido actualizado con éxito.']);
}

public function destroyMatch($id)
{
    Matches::findOrFail($id)->delete();
    return back()->with('success', 'Partido eliminado correctamente.');
}

}