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
use Database\Seeders\DemoTeamSeeder;




class TeamsController extends Controller
{

    public function index()
    {
    $teams = Team::where('user_id', Auth::id())->get(); // Filtrar por usuario autenticado
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
    
        // 📊 Estadísticas generales
        $victorias = $partidosLiga->where('resultado', 'Victoria')->count();
        $empates = $partidosLiga->where('resultado', 'Empate')->count();
        $derrotas = $partidosLiga->where('resultado', 'Derrota')->count();
        $puntos = ($victorias * 3) + $empates;
        $golesFavor = $partidosLiga->sum('goles_a_favor');
        $golesContra = $partidosLiga->sum('goles_en_contra');
        $valoracionMedia = round($partidosLiga->pluck('actuacion_equipo')->filter()->avg() ?? 0, 2);
    
        // 📊 Estadísticas individuales (para el Top 5)
        $topStats = [];
    
        foreach ($team->players as $player) {
            $statsJugador = MatchPlayerStat::where('player_id', $player->id)
                ->whereHas('match', function ($q) use ($team) {
                    $q->where('team_id', $team->id)->where('tipo', 'liga');
                })
                ->get();
    
            $topStats[] = [
                'jugador' => $player,
                'goles' => $statsJugador->sum('goles'),
                'asistencias' => $statsJugador->sum('asistencias'),
                'minutos' => $statsJugador->sum('minutos_jugados'),
                'valoracion' => round($statsJugador->avg('valoracion') ?? 0, 2),
                'amarillas' => $statsJugador->sum('tarjetas_amarillas'),
                'rojas' => $statsJugador->sum('tarjetas_rojas'),
            ];
        }
    
        // Top 5 por cada categoría
        $topGoles = collect($topStats)->sortByDesc('goles')->take(5);
        $topAsistencias = collect($topStats)->sortByDesc('asistencias')->take(5);
        $topMinutos = collect($topStats)->sortByDesc('minutos')->take(5);
        $topValoracion = collect($topStats)->sortByDesc('valoracion')->take(5);
        $topTarjetas = collect($topStats)->sortByDesc(fn($s) => $s['amarillas'] + $s['rojas'])->take(5);
    
        $stats = [
            'partidos_jugados' => $partidosLiga->count(),
            'victorias' => $victorias,
            'empates' => $empates,
            'derrotas' => $derrotas,
            'puntos' => $puntos,
            'goles_favor' => $golesFavor,
            'goles_contra' => $golesContra,
            'valoracion_media' => $valoracionMedia,
            'tarjetas_amarillas' => $topStats ? array_sum(array_column($topStats, 'amarillas')) : 0,
            'tarjetas_rojas' => $topStats ? array_sum(array_column($topStats, 'rojas')) : 0,
        ];
    
        return view('dashboard.team_show', compact(
            'team', 'allPlayers', 'stats',
            'partidosAmistosos', 'partidosLiga', 'hayLiga',
            'topGoles', 'topAsistencias', 'topMinutos', 'topValoracion', 'topTarjetas'
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

public function createDemo()
{
    $userId = Auth::id(); // Igual que en store()

    // Ejecutar seeder con el ID del usuario actual
    (new DemoTeamSeeder())->run($userId);

    return redirect()->route('teams.index')->with('success', '¡Plantilla de demostración creada con éxito!');
}
}
