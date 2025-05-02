<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Player;
use App\Models\Matches;
use App\Models\RivalLiga;
use App\Models\MatchPlayerStat;
use Faker\Factory as Faker;

class DemoTeamSeeder extends Seeder
{
    public function run($userId = null)
    {
        $faker = Faker::create();

        $userId = $userId ?? \App\Models\User::first()?->id ?? 1;

        $count = Team::where('nombre', 'LIKE', 'Plantilla de demostración%')->count() + 1;

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

        // Crear partidos amistosos con estadísticas y valoraciones
        for ($i = 0; $i < 3; $i++) {
            $match = Matches::create([
                'team_id' => $team->id,
                'tipo' => 'amistoso',
                'equipo_rival' => $faker->words(2, true), // Dos palabras cortas
                'fecha_partido' => $faker->date('Y-m-d'), // Solo fecha
                'local' => (bool)rand(0, 1),
                'goles_a_favor' => rand(0, 5),
                'goles_en_contra' => rand(0, 5),
                'resultado' => $faker->randomElement(['Victoria', 'Empate', 'Derrota']),
                'actuacion_equipo' => rand(5, 10),
            ]);

            foreach ($players as $player) {
                if (rand(0, 1)) {
                    MatchPlayerStat::create([
                        'player_id' => $player->id,
                        'match_id' => $match->id,
                        'titular' => rand(0, 1),
                        'minutos_jugados' => rand(10, 90),
                        'goles' => rand(0, 3),
                        'asistencias' => rand(0, 2),
                        'tarjetas_amarillas' => rand(0, 2),
                        'tarjetas_rojas' => rand(0, 1),
                        'valoracion' => rand(5, 10),
                    ]);
                }
            }
        }

        // Crear liga con partidos, estadísticas y valoraciones
        for ($jornada = 1; $jornada <= 5; $jornada++) {
            $rival = RivalLiga::create([
                'team_id' => $team->id,
                'nombre_equipo' => $faker->words(2, true), // Dos palabras cortas
                'jornada' => $jornada,
            ]);

            $match = Matches::create([
                'team_id' => $team->id,
                'tipo' => 'liga',
                'rival_liga_id' => $rival->id,
                'equipo_rival' => $rival->nombre_equipo,
                'fecha_partido' => $faker->date('Y-m-d'), // Solo fecha
                'local' => true,
                'goles_a_favor' => rand(0, 5),
                'goles_en_contra' => rand(0, 5),
                'resultado' => $faker->randomElement(['Victoria', 'Empate', 'Derrota']),
                'actuacion_equipo' => rand(5, 10),
            ]);

            foreach ($players as $player) {
                if (rand(0, 1)) {
                    MatchPlayerStat::create([
                        'player_id' => $player->id,
                        'match_id' => $match->id,
                        'titular' => rand(0, 1),
                        'minutos_jugados' => rand(10, 90),
                        'goles' => rand(0, 3),
                        'asistencias' => rand(0, 2),
                        'tarjetas_amarillas' => rand(0, 2),
                        'tarjetas_rojas' => rand(0, 1),
                        'valoracion' => rand(5, 10),
                    ]);
                }
            }
        }
    }
}