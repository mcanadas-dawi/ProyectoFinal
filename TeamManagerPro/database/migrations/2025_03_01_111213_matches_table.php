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
            $table->foreignId('team_id')->constrained()->onDelete('cascade'); // Relacionado con una plantilla
            $table->integer('numero_jornada');
            $table->string('equipo_rival');
            $table->date('fecha_partido');
            $table->enum('resultado', ['Victoria', 'Empate', 'Derrota'])->nullable();
            $table->integer('goles_a_favor')->default(0);
            $table->integer('goles_en_contra')->default(0);
            $table->decimal('actuacion_equipo', 4, 2)->nullable(); // Evaluación del equipo
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
