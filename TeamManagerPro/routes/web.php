<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/teams', [DashboardController::class, 'storeTeam'])->name('teams.store');
    Route::post('/players', [DashboardController::class, 'storePlayer'])->name('players.store');
    Route::post('/matches', [DashboardController::class, 'storeMatch'])->name('matches.store');
    Route::delete('/teams/{id}', [DashboardController::class, 'destroyTeam'])->name('teams.destroy');
    Route::get('/teams/{id}', [DashboardController::class, 'show'])->name('teams.show');
    Route::delete('/players/{id}', [DashboardController::class, 'destroyPlayer'])->name('players.destroy');
    Route::patch('/players/{id}', [DashboardController::class, 'updatePlayer'])->name('players.update');
    Route::patch('/matches/{id}', [DashboardController::class, 'updateMatch'])->name('matches.update');
    Route::delete('/matches/{id}', [DashboardController::class, 'destroyMatch'])->name('matches.destroy');
    Route::post('/players/add-to-team', [DashboardController::class, 'addPlayerToTeam'])->name('players.addToTeam');
    Route::get('/matches/{match}/rate', [DashboardController::class, 'ratePlayers'])->name('matches.ratePlayers');
    Route::post('/matches/{match}/rate', [DashboardController::class, 'saveRatings'])->name('matches.saveRatings');

});

require __DIR__.'/auth.php';
