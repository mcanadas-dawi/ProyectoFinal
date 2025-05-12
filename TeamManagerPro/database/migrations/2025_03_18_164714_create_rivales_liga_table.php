<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
     public function up(): void
    {
        Schema::create('rivales_liga', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_equipo');
            $table->integer('jornada');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Añadir foreign key en matches después de crear la tabla rivales_liga
        Schema::table('matches', function (Blueprint $table) {
            $table->foreign('rival_liga_id')->references('id')->on('rivales_liga')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['rival_liga_id']);
        });
        Schema::dropIfExists('rivales_liga');
    }
};

