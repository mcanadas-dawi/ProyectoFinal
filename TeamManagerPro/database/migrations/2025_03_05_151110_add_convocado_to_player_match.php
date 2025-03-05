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
        Schema::table('player_match', function (Blueprint $table) {
            $table->boolean('convocado')->default(false); // Campo para convocatoria
        });
    
        Schema::table('matches', function (Blueprint $table) {
            $table->string('formacion')->nullable(); // Guarda la formación elegida
        });
    }
    
    public function down()
    {
        Schema::table('player_match', function (Blueprint $table) {
            $table->dropColumn('convocado');
        });
    
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn('formacion');
        });
    }
    
};
