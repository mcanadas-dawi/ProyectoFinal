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
        $posiciones = [
        'Portero' => 2,
        'Defensa' => 5,
        'Centrocampista' => 5,
        'Delantero' => 4
        ];
        $dorsal = 1;

        foreach ($posiciones as $posicion => $cantidad) {
        for ($i = 0; $i < $cantidad; $i++) {
            $player = Player::create([
                'nombre' => $faker->firstName,
                'apellido' => $faker->lastName,
                'dni' => strtoupper($faker->randomNumber(8, true) . $faker->randomLetter()),
                'dorsal' => $dorsal++,
                'posicion' => $posicion,
                'perfil' => $faker->randomElement(['Diestro', 'Zurdo']),
                'fecha_nacimiento' => $faker->date('Y-m-d', '2005-12-31'),
            ]);
            
            $team->players()->attach($player->id);
            $players[] = $player;
        }
    }
        
       // Crear partidos amistosos con estadísticas y valoraciones

            for ($i = 0; $i < 3; $i++) {
                $golesFavor = rand(0, 5);
                $golesContra = rand(0, 5);

                $match = Matches::create([
                    'team_id' => $team->id,
                    'tipo' => 'amistoso',
                    'equipo_rival' => $faker->randomElement($equiposPrimeraDivision),
                    'fecha_partido' => $faker->dateTimeBetween('-6 months', '+6 months')->format('Y-m-d'),
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

                // Usar el mismo método que los partidos de liga
                $this->generarEstadisticasJugadores($match, $players, $faker);
            }

        $rivalesLiga = $faker->randomElements($equiposPrimeraDivision, 5);
        
        // Decidir aleatoriamente cuántos partidos serán local en la primera vuelta (2 o 3)
        $partidosLocalesIda = rand(2, 3);
        
        // Generar un array aleatorio con los índices de partidos locales
        $indicesLocalesIda = (array) array_rand(range(0, 4), $partidosLocalesIda);
        
        // PRIMERA VUELTA (Jornadas 1-5)
        for ($jornada = 1; $jornada <= 5; $jornada++) {
            $indice = $jornada - 1;
            $nombreRival = $rivalesLiga[$indice];
            
            // Determinar si este partido es local en la ida
            $esLocalEnIda = in_array($indice, $indicesLocalesIda);
            
            $rival = RivalLiga::create([
                'team_id' => $team->id,
                'nombre_equipo' => $nombreRival,
                'jornada' => $jornada,
            ]);

            $golesFavor = rand(0, 5);
            $golesContra = rand(0, 5);

            $match = Matches::create([
                'team_id' => $team->id,
                'tipo' => 'liga',
                'rival_liga_id' => $rival->id,
                'equipo_rival' => $nombreRival,
                'fecha_partido' => $faker->dateTimeBetween('-6 months', '+1 month')->format('Y-m-d'),
                'local' => $esLocalEnIda,  // Asignar según el array de índices locales
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

            // Generar estadísticas de jugadores para este partido
            $this->generarEstadisticasJugadores($match, $players, $faker);
        }
        
        // SEGUNDA VUELTA (Jornadas 6-10)
        for ($jornada = 6; $jornada <= 10; $jornada++) {
            $indice = $jornada - 6;  // 0-4 para los mismos rivales
            $nombreRival = $rivalesLiga[$indice];
            
            // En la vuelta, invertir si es local o visitante
            $esLocalEnVuelta = !in_array($indice, $indicesLocalesIda);
            
            $rival = RivalLiga::create([
                'team_id' => $team->id,
                'nombre_equipo' => $nombreRival,
                'jornada' => $jornada,
            ]);

            $golesFavor = rand(0, 5);
            $golesContra = rand(0, 5);

            $match = Matches::create([
                'team_id' => $team->id,
                'tipo' => 'liga',
                'rival_liga_id' => $rival->id,
                'equipo_rival' => $nombreRival,
                'fecha_partido' => $faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
                'local' => $esLocalEnVuelta,  // Invertir respecto a la ida
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

            // Generar estadísticas de jugadores para este partido
            $this->generarEstadisticasJugadores($match, $players, $faker);
        }

        // Actualizar estadísticas globales por jugador
        $this->actualizarEstadisticasGlobales($players, $team);
    }

    // Método auxiliar para generar estadísticas de jugadores
    private function generarEstadisticasJugadores($match, $players, $faker)
    {
        $porteros = array_filter($players, function ($player) {
            return $player->posicion === 'Portero';
        });

        if (count($porteros) > 0) {
            // Elegir un portero al azar para ser titular
            $porteroTitular = $faker->randomElement($porteros);
            
            // Registrar las estadísticas para el portero titular
            MatchPlayerStat::create([
                'player_id' => $porteroTitular->id,
                'match_id' => $match->id,
                'titular' => true,
                'minutos_jugados' => rand(70, 90),
                'goles' => 0, 
                'goles_encajados' => $match->goles_en_contra, // Todos los goles encajados al portero titular
                'asistencias' => 0,
                'tarjetas_amarillas' => rand(0, 10) < 2 ? 1 : 0,
                'tarjetas_rojas' => rand(0, 20) < 1 ? 1 : 0,
                'valoracion' => match ($match->resultado) {
                    'Victoria' => rand(7, 10),
                    'Empate' => rand(5, 7),
                    'Derrota' => rand(3, 5),
                },
            ]);
            
            // Quitar al portero titular de la lista para no procesarlo de nuevo
            $porteros = array_filter($porteros, function ($p) use ($porteroTitular) {
                return $p->id !== $porteroTitular->id;
            });
        }
        
        // Procesar el resto de jugadores (incluido el portero suplente)
        foreach ($players as $player) {
            // Si es el portero titular, ya lo hemos procesado
            if ($player->posicion === 'Portero' && isset($porteroTitular) && $player->id === $porteroTitular->id) {
                continue;
            }
            
            // Decidir si el jugador participa en el partido (50% probabilidad, excepto portero suplente)
            $participa = $player->posicion === 'Portero' ? (rand(0, 5) > 0 ? false : true) : (rand(0, 1) == 1);
            
            if ($participa) {
                $esTitular = $player->posicion !== 'Portero' ? rand(0, 1) : false; // Portero suplente nunca titular
                
                MatchPlayerStat::create([
                    'player_id' => $player->id,
                    'match_id' => $match->id,
                    'titular' => $esTitular,
                    'minutos_jugados' => $esTitular ? rand(60, 90) : rand(0, 30),
                    'goles' => $player->posicion === 'Portero' ? 0 : rand(0, ($player->posicion === 'Delantero' ? 2 : 1)),
                    'goles_encajados' => 0, // Solo el titular recibe goles
                    'asistencias' => rand(0, ($player->posicion === 'Centrocampista' ? 2 : 1)),
                    'tarjetas_amarillas' => rand(0, 10) < 2 ? 1 : 0,
                    'tarjetas_rojas' => rand(0, 20) < 1 ? 1 : 0,
                    'valoracion' => match ($match->resultado) {
                        'Victoria' => rand(6, 10),
                        'Empate' => rand(5, 7),
                        'Derrota' => rand(3, 6),
                    },
                ]);
            }
        }
    }

    // Método auxiliar para actualizar estadísticas globales
        private function actualizarEstadisticasGlobales($players, $team)
    {        foreach ($players as $player) {
            // Crear una nueva consulta para cada jugador
            $stats = MatchPlayerStat::where('player_id', $player->id)
                ->whereHas('match', function ($q) use ($team) {
                    $q->where('team_id', $team->id);
                });

            // Obtener todas las estadísticas del jugador
            $matchStats = $stats->get();
            
            // Calcular valores correctos
            $jugado = $matchStats->count();
            $titular = $matchStats->sum('titular');
            $suplente = $jugado - $titular;
            
            // Calcular valores sumados correctamente para evitar problemas de redondeo o conteo doble
            $minutos = $matchStats->sum('minutos_jugados');
            $goles = $matchStats->sum('goles');
            $goles_encajados = $matchStats->sum('goles_encajados');
            $asistencias = $matchStats->sum('asistencias');
            $amarillas = $matchStats->sum('tarjetas_amarillas');
            $rojas = $matchStats->sum('tarjetas_rojas');
            $valoracion = $jugado > 0 ? $matchStats->avg('valoracion') : 0;

            PlayerTeamStats::updateOrCreate(
                [
                    'player_id' => $player->id,
                    'team_id' => $team->id,
                ],
                [
                    'minutos_jugados' => $minutos,
                    'goles' => $goles,
                    'goles_encajados' => $goles_encajados,
                    'asistencias' => $asistencias,
                    'titular' => $titular,
                    'suplente' => $suplente,
                    'valoracion' => round($valoracion ?? 5, 1),
                    'tarjetas_amarillas' => $amarillas,
                    'tarjetas_rojas' => $rojas,
                ]
            );
        }
    }
}