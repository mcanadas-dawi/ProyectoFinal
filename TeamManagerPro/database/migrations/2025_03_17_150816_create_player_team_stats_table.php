<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('player_team_stats', function (Blueprint $table) {
        $table->id();
        $table->foreignId('player_id')->constrained()->onDelete('cascade');
        $table->foreignId('team_id')->constrained()->onDelete('cascade');
        $table->integer('minutos_jugados')->default(0);
        $table->integer('goles')->default(0);
        $table->integer('asistencias')->default(0);
        $table->integer('tarjetas_amarillas')->default(0);
        $table->integer('tarjetas_rojas')->default(0);
        $table->integer('titular')->default(0);
        $table->integer('suplente')->default(0);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('player_team_stats');
}

};
