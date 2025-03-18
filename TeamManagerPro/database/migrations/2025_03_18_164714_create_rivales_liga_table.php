<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rivales_liga', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_equipo');
            $table->integer('jornada');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rivales_liga');
    }
};

