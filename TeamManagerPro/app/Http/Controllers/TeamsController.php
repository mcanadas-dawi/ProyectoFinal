<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Player;
use App\Models\Matches;
use App\Models\RivalLiga;
use App\Models\MatchPlayerStat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;



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
        ->with(['players', 'matches'])
        ->firstOrFail();

    session(['current_team_id' => $team->id]);

    $partidosAmistosos = $team->matches()->where('tipo', 'amistoso')->get();
    $partidosLiga = $team->matches()->where('tipo', 'liga')->get();

    $hayLiga = $partidosLiga->isNotEmpty();

    $allPlayers = Player::whereNotIn('id', $team->players->pluck('id'))->get();

    // 📊 Cálculo de estadísticas
    $victorias = $partidosLiga->where('resultado', 'Victoria')->count();
    $empates = $partidosLiga->where('resultado', 'Empate')->count();
    $derrotas = $partidosLiga->where('resultado', 'Derrota')->count();
    $puntos = ($victorias * 3) + $empates;

    $golesFavor = $partidosLiga->sum('goles_a_favor');
    $golesContra = $partidosLiga->sum('goles_en_contra');

    $valoracionMedia = $partidosLiga->pluck('actuacion_equipo')->filter()->avg() ?? 0;
    $valoracionMedia = round($valoracionMedia, 2);

    // Tarjetas (desde match_player_stats)
    $tarjetasAmarillas = 0;
    $tarjetasRojas = 0;

    foreach ($team->players as $player) {
        $statsJugador = MatchPlayerStat::where('player_id', $player->id)
            ->whereHas('match', function ($q) use ($team) {
                $q->where('team_id', $team->id)
                  ->where('tipo', 'liga');
            })
            ->get();

        $tarjetasAmarillas += $statsJugador->sum('tarjetas_amarillas');
        $tarjetasRojas += $statsJugador->sum('tarjetas_rojas');
    }

    $stats = [
        'victorias' => $victorias,
        'empates' => $empates,
        'derrotas' => $derrotas,
        'puntos' => $puntos,
        'goles_favor' => $golesFavor,
        'goles_contra' => $golesContra,
        'valoracion_media' => $valoracionMedia,
        'tarjetas_amarillas' => $tarjetasAmarillas,
        'tarjetas_rojas' => $tarjetasRojas,
    ];

    return view('dashboard.team_show', compact(
        'team', 'allPlayers', 'stats', 'partidosAmistosos', 'partidosLiga', 'hayLiga'
    ));
}


    

    public function store(Request $request)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'modalidad' => 'required|string',
    ]);

    $team = new Team();
    $team->nombre = $validated['nombre'];
    $team->modalidad = $validated['modalidad'];
    $team->user_id = Auth::id();
    $team->save();

    return redirect()->route('teams.index')->with('success', 'Plantilla creada con éxito');
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
