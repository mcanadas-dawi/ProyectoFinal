<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Player;
use App\Models\Matches;
use App\Models\RivalLiga;
use App\Models\MatchPlayerStat;
use Faker\Factory as Faker;
use App\Models\PlayerTeamStats;

class DemoTeamSeeder extends Seeder
{
    public function run($userId = null)
    {
        $faker = Faker::create('es_ES');

        $userId = $userId ?? \App\Models\User::first()?->id ?? 1;

         // Contar las plantillas creadas por el usuario autenticado
         $count = Team::where('user_id', $userId)
         ->where('nombre', 'LIKE', 'Plantilla de demostración%')
         ->count() + 1;

        // Crear la plantilla
        $team = Team::create([
            'nombre' => "Plantilla de demostración {$count}",
            'modalidad' => 'F11',
            'user_id' => $userId,
        ]);

        // Crear jugadores y añadirlos directamente al equipo
        $players = [];
        for ($i = 1; $i <= 15; $i++) {
            $player = Player::create([
                'team_id' => $team->id,
                'nombre' => $faker->firstName,
                'apellido' => $faker->lastName,
                'dni' => strtoupper($faker->randomNumber(8, true) . $faker->randomLetter()),
                'dorsal' => $i,
                'posicion' => $faker->randomElement(['Portero', 'Defensa', 'Centrocampista', 'Delantero']),
                'perfil' => $faker->randomElement(['Diestro', 'Zurdo']),
                'fecha_nacimiento' => $faker->date('Y-m-d', '2005-12-31'),
            ]);
            
            $team->players()->attach($player->id); // Asociación en tabla pivote
            
            $players[] = $player;
            
        }
        $equiposPrimeraDivision = [
            'Real Madrid',
            'FC Barcelona',
            'Atlético de Madrid',
            'Sevilla FC',
            'Real Sociedad',
            'Real Betis',
            'Villarreal CF',
            'Athletic Club',
            'Valencia CF',
            'Celta de Vigo',
            'RCD Espanyol',
            'Getafe CF',
            'Rayo Vallecano',
            'CA Osasuna',
            'Real Valladolid',
            'UD Almería',
            'Cádiz CF',
            'RCD Mallorca',
            'Granada CF',
            'UD Las Palmas',
        ];
       // Crear partidos amistosos con estadísticas y valoraciones
        for ($i = 0; $i < 3; $i++) {
            $golesFavor = rand(0, 5);
            $golesContra = rand(0, 5);

            $match = Matches::create([
                'team_id' => $team->id,
                'tipo' => 'amistoso',
                'equipo_rival' => $faker->randomElement($equiposPrimeraDivision), // Seleccionar un equipo aleatorio
                'fecha_partido' => $faker->dateTimeBetween('-6 months', '+6 months')->format('Y-m-d'), // Fecha dentro de 6 meses antes o después
                'local' => (bool)rand(0, 1),
                'goles_a_favor' => $golesFavor,
                'goles_en_contra' => $golesContra,
                'resultado' => 'Pendiente', 
                'actuacion_equipo' => 0,     
            ]);

            // Calcular resultado real
            if ($golesFavor > $golesContra) {
                $match->resultado = 'Victoria';
                $match->actuacion_equipo = rand(7, 10);
            } elseif ($golesFavor == $golesContra) {
                $match->resultado = 'Empate';
                $match->actuacion_equipo = rand(5, 7);
            } else {
                $match->resultado = 'Derrota';
                $match->actuacion_equipo = rand(1, 4);
            }
            $match->save();

            foreach ($players as $player) {
                if (rand(0, 1)) {
                    MatchPlayerStat::create([
                        'player_id' => $player->id,
                        'match_id' => $match->id,
                        'titular' => rand(0, 1),
                        'minutos_jugados' => rand(10, 90),
                        'goles' => rand(0, 3),
                        'asistencias' => rand(0, 2),
                        'tarjetas_amarillas' => rand(1, 100) <= 25 ? 1 : 0,
                        'tarjetas_rojas' => rand(1, 100) <= 10 ? 1 : 0,
                        'valoracion' => match ($match->resultado) {
                            'Victoria' => rand(7, 10),
                            'Empate' => rand(5, 7),
                            'Derrota' => rand(1, 4),
                        },
                    ]);
                }
            }
        }


        // Crear liga con partidos, estadísticas y valoraciones
        for ($jornada = 1; $jornada <= 5; $jornada++) {
            $rival = RivalLiga::create([
                'team_id' => $team->id,
                'nombre_equipo' => $faker->words(2, true),
                'jornada' => $jornada,
            ]);

            $golesFavor = rand(0, 5);
            $golesContra = rand(0, 5);

            $match = Matches::create([
                'team_id' => $team->id,
                'tipo' => 'liga',
                'rival_liga_id' => $rival->id,
                'equipo_rival' => $faker->randomElement($equiposPrimeraDivision), // Seleccionar un equipo aleatorio
                'fecha_partido' => $faker->dateTimeBetween('-6 months', '+6 months')->format('Y-m-d'), // Fecha dentro de 6 meses antes o después
                'local' => true,
                'goles_a_favor' => $golesFavor,
                'goles_en_contra' => $golesContra,
                'resultado' => 'Pendiente', 
                'actuacion_equipo' => 0,     
            ]);

            // Calcular resultado real
            if ($golesFavor > $golesContra) {
                $match->resultado = 'Victoria';
                $match->actuacion_equipo = rand(7, 10);
            } elseif ($golesFavor == $golesContra) {
                $match->resultado = 'Empate';
                $match->actuacion_equipo = rand(5, 7);
            } else {
                $match->resultado = 'Derrota';
                $match->actuacion_equipo = rand(1, 4);
            }
            $match->save();

            foreach ($players as $player) {
                if (rand(0, 1)) {
                    MatchPlayerStat::create([
                        'player_id' => $player->id,
                        'match_id' => $match->id,
                        'titular' => rand(0, 1),
                        'minutos_jugados' => rand(10, 90),
                        'goles' => rand(0, 3),
                        'asistencias' => rand(0, 2),
                        'tarjetas_amarillas' => rand(1, 100) <= 25 ? 1 : 0,
                        'tarjetas_rojas' => rand(1, 100) <= 10 ? 1 : 0,
                        'valoracion' => match ($match->resultado) {
                            'Victoria' => rand(7, 10),
                            'Empate' => rand(5, 7),
                            'Derrota' => rand(1, 4),
                        },
                    ]);
                }
            }
        }
        // ✅ ACTUALIZAR ESTADÍSTICAS GLOBALES POR JUGADOR
        foreach ($players as $player) {
            $stats = MatchPlayerStat::where('player_id', $player->id)
                ->whereHas('match', function ($q) use ($team) {
                    $q->where('team_id', $team->id);
                });

            $jugado = $stats->count();
            $titular = $stats->sum('titular');
            $suplente = $jugado - $titular;

            PlayerTeamStats::updateOrCreate(
                [
                    'player_id' => $player->id,
                    'team_id' => $team->id,
                ],
                [
                    'minutos_jugados' => $stats->sum('minutos_jugados'),
                    'goles' => $stats->sum('goles'),
                    'asistencias' => $stats->sum('asistencias'),
                    'titular' => $titular,
                    'suplente' => $suplente,
                    'valoracion' => round($stats->avg('valoracion') ?? 0, 2),
                    'tarjetas_amarillas' => $stats->sum('tarjetas_amarillas'),
                    'tarjetas_rojas' => $stats->sum('tarjetas_rojas'),
                ]
            );
        }

    }
}    
