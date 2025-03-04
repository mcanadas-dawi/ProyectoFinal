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
        Schema::table('matches', function (Blueprint $table) {
            // Asegurar que el número de jornada sea único dentro de cada equipo
            $table->unique(['team_id', 'numero_jornada']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // Eliminar la restricción única si hacemos rollback
            $table->dropUnique(['team_id', 'numero_jornada']);
        });
    }
};

