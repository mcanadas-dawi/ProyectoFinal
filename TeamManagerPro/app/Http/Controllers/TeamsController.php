<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Player;
use App\Models\Matches;
use App\Models\RivalLiga;
use Illuminate\Support\Facades\Auth;


class TeamsController extends Controller
{
    public function index()
    {
        $teams = Team::all();
        return view('dashboard.index', compact('teams'));
    }

    public function show($id)
    {
        $team = Team::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('players')
            ->firstOrFail();

        $allPlayers = Player::whereNotIn('id', $team->players->pluck('id'))->get();
        $stats = $this->getTeamStats($id);

        return view('dashboard.team_show', compact('team', 'allPlayers', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'modalidad' => 'required|string|max:50',
        ]);

        Team::create($request->all());

        return redirect()->route('dashboard')->with('success', 'Equipo creado correctamente.');
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return redirect()->route('dashboard')->with('success', 'Equipo eliminado correctamente.');
    }

    public function getTeamStats($teamId)
{
    $team = Team::with(['matches' => function ($query) {
        $query->where('tipo', 'liga'); // Solo contar partidos de liga
    }])->findOrFail($teamId);

    $victorias = $team->matches->where('resultado', 'Victoria')->count();
    $empates = $team->matches->where('resultado', 'Empate')->count();
    $derrotas = $team->matches->where('resultado', 'Derrota')->count();

    $puntos = ($victorias * 3) + ($empates * 1);
    $golesFavor = $team->matches->sum('goles_a_favor');
    $golesContra = $team->matches->sum('goles_en_contra');

    return [
        'victorias' => $victorias,
        'empates' => $empates,
        'derrotas' => $derrotas,
        'puntos' => $puntos,
        'golesFavor' => $golesFavor,
        'golesContra' => $golesContra,
    ];
}
}
