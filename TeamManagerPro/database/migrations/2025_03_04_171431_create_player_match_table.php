<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('player_match', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->integer('valoracion')->nullable(); // Puntuación del jugador en el partido (1-10)
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('player_match');
    }
};

