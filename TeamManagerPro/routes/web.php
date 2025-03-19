<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MatchesController;
use App\Http\Controllers\RivalesLigaController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/profile', function () {
    return view('profile.edit');
})->name('profile.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 📌 Rutas de Equipos (Teams)
    Route::post('/teams', [DashboardController::class, 'storeTeam'])->name('teams.store');
    Route::delete('/teams/{id}', [DashboardController::class, 'destroyTeam'])->name('teams.destroy');
    Route::get('/teams/{id}', [DashboardController::class, 'show'])->name('teams.show');

    // 📌 Rutas de Jugadores (Players)
    Route::post('/players', [DashboardController::class, 'storePlayer'])->name('players.store');
    Route::delete('/players/{id}', [DashboardController::class, 'destroyPlayer'])->name('players.destroy');
    Route::patch('/players/{id}', [DashboardController::class, 'updatePlayer'])->name('players.update');
    Route::post('/players/add-to-team', [DashboardController::class, 'addPlayerToTeam'])->name('players.addToTeam');

    // 📌 Rutas de Partidos (Matches) → Se mueven a MatchesController
    Route::resource('matches', MatchesController::class);
    Route::patch('/matches/{id}', [MatchesController::class, 'updateMatch'])->name('matches.update');
    Route::delete('/matches/{id}', [MatchesController::class, 'destroyMatch'])->name('matches.destroy');
    Route::post('/matches', [MatchesController::class, 'store'])->name('matches.store'); 

    // 📌 Rutas para "Valorar Jugadores" (ratePlayers) → Se mueven a MatchesController
    Route::get('/matches/{match}/rate', [MatchesController::class, 'ratePlayers'])->name('matches.ratePlayers'); 
    Route::post('/matches/{match}/rate', [MatchesController::class, 'saveRatings'])->name('matches.saveRatings');

    // 📌 Rutas de Convocatoria → Se mueven a MatchesController
    Route::post('/matches/convocatoria', [MatchesController::class, 'storeConvocatoria'])->name('matches.convocatoria');
    Route::post('/matches/{match}/convocatoria', [MatchesController::class, 'updateConvocatoria'])->name('matches.updateConvocatoria');

    // 📌 Rutas de Alineación → Se mueven a MatchesController
    Route::post('/matches/{match}/save-alineacion', [MatchesController::class, 'saveAlineacion'])->name('matches.saveAlineacion');
    Route::get('/matches/{match}/get-alineacion', [MatchesController::class, 'getAlineacion'])->name('matches.getAlineacion');

    // 📌 Verificar jornada
    Route::get('/api/verify-jornada/{teamId}/{numeroJornada}', [DashboardController::class, 'verifyJornada']);

    // 📌 Rutas para "rivales_liga"
    Route::resource('rivales_liga', RivalesLigaController::class)->only(['store']);

    // 📌 Mostrar equipo con partidos (debe estar en MatchesController)
    Route::get('/teams/{team}', [MatchesController::class, 'show'])->name('teams.show');
});

require __DIR__.'/auth.php';
