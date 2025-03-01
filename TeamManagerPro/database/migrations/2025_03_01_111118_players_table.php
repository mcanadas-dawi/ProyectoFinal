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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->integer('dorsal');
            $table->date('fecha_nacimiento');
            $table->enum('posicion', ['Portero', 'Defensa', 'Centrocampista', 'Delantero']);
            $table->enum('perfil', ['Diestro', 'Zurdo']);
            $table->integer('minutos_jugados')->default(0);
            $table->integer('goles')->default(0);
            $table->integer('asistencias')->default(0);
            $table->integer('goles_encajados')->default(0); // Solo aplicable a porteros
            $table->integer('titular')->default(0); 
            $table->integer('suplente')->default(0);
            $table->decimal('valoracion', 4, 2)->default(0); // Promedio de rendimiento en partidos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
