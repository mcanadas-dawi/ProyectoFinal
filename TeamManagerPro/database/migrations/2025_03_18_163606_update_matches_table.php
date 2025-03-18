<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('matches', function (Blueprint $table) {
            // 🔹 Eliminar la restricción única si existe
            DB::statement('DROP INDEX IF EXISTS matches_team_id_numero_jornada_unique');

            // 🔹 Eliminar la columna `numero_jornada`
            if (Schema::hasColumn('matches', 'numero_jornada')) {
                $table->dropColumn('numero_jornada');
            }

            // 🔹 Agregar los nuevos campos
            $table->enum('tipo', ['amistoso', 'liga'])->default('amistoso')->after('team_id');
            $table->foreignId('rival_liga_id')->nullable()->constrained('rivales_liga')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::table('matches', function (Blueprint $table) {
            // 🔹 Restaurar la columna
            $table->integer('numero_jornada')->nullable();

            // 🔹 Restaurar la restricción única
            $table->unique(['team_id', 'numero_jornada']);

            // 🔹 Eliminar los nuevos campos
            $table->dropForeign(['rival_liga_id']);
            $table->dropColumn('rival_liga_id');
            $table->dropColumn('tipo');
        });
    }
};


