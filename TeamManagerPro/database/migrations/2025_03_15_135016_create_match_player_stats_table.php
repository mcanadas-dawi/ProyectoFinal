<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('match_player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->onDelete('cascade'); // Relación con el partido
            $table->foreignId('player_id')->constrained()->onDelete('cascade'); // Relación con el jugador
            $table->boolean('titular')->default(false);
            $table->integer('minutos_jugados')->default(0);
            $table->integer('goles')->default(0);
            $table->integer('goles_encajados')->default(0);
            $table->integer('asistencias')->default(0);
            $table->integer('tarjetas_amarillas')->default(0);
            $table->integer('tarjetas_rojas')->default(0);
            $table->decimal('valoracion', 4, 2)->nullable();
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('match_player_stats');
    }
    
};
