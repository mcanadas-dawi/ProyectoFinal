<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\PlayersController;
use App\Http\Controllers\MatchesController;
use App\Http\Controllers\ConvocatoriasController;
use App\Http\Controllers\AlineacionesController;
use App\Http\Controllers\MatchPlayerStatController;
use App\Http\Controllers\RivalesLigaController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 📌 Dashboard
    Route::get('/dashboard', [TeamsController::class, 'index'])->name('dashboard');

    // 📌 Equipos
    Route::post('/teams', [TeamsController::class, 'store'])->name('teams.store');
    Route::delete('/teams/{id}', [TeamsController::class, 'destroy'])->name('teams.destroy');
    Route::get('/teams/{id}', [TeamsController::class, 'show'])->name('teams.show');
    Route::resource('teams', TeamsController::class);


    // 📌 Jugadores
    Route::post('/players', [PlayersController::class, 'store'])->name('players.store');
    Route::delete('/players/{id}', [PlayersController::class, 'destroy'])->name('players.destroy');
    Route::patch('/players/{id}', [PlayersController::class, 'update'])->name('players.update');
    Route::post('/players/add-to-team', [PlayersController::class, 'addPlayerToTeam'])->name('players.addToTeam');

    // 📌 Partidos
    Route::post('/matches', [MatchesController::class, 'store'])->name('matches.store');
    Route::patch('/matches/{id}', [MatchesController::class, 'update'])->name('matches.update');
    Route::delete('/matches/{id}', [MatchesController::class, 'destroy'])->name('matches.destroy');
    Route::resource('matches', MatchesController::class);

    // 📌 Convocatorias
    Route::post('/matches/convocatoria', [ConvocatoriasController::class, 'store'])->name('matches.convocatoria');
    Route::post('/matches/{match}/convocatoria', [ConvocatoriasController::class, 'update'])->name('matches.updateConvocatoria');
    Route::get('/matches/{matchId}/get-convocados', [ConvocatoriasController::class, 'getConvocados']);

    // 📌 Alineaciones
    Route::get('/matches/{match}/get-alineacion', [AlineacionesController::class, 'get'])->name('matches.getAlineacion');

    // 📌 Estadísticas de Jugadores en Partidos
    Route::post('/matches/{match}/stats', [MatchPlayerStatController::class, 'store'])->name('matches.stats.store');

    // 📌 Valoraciones de Jugadores
    Route::get('/matches/{match}/rate', [MatchPlayerStatController::class, 'ratePlayers'])->name('matches.ratePlayers'); 
    Route::post('/matches/{match}/rate', [MatchPlayerStatController::class, 'saveRatings'])->name('matches.saveRatings');

    // 📌 Rivales de Liga
    Route::resource('rivales_liga', RivalesLigaController::class)->only(['store', 'update', 'destroy']);
});

require __DIR__.'/auth.php';
