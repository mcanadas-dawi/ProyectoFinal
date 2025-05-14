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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->integer('rival_liga_id')->nullable();
            $table->string('tipo');
            $table->string('equipo_rival');
            $table->date('fecha_partido');
            $table->integer('goles_a_favor')->nullable();
            $table->integer('goles_en_contra')->nullable();
            $table->string('resultado')->nullable();
            $table->float('actuacion_equipo')->nullable();
            $table->boolean('local')->default(true);
            $table->string('alineacion_imagen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
