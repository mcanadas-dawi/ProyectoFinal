<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateMatchesTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('matches');

        Schema::create('matches', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('rival_liga_id')->nullable();
            $table->string('tipo');
            $table->string('equipo_rival')->nullable();
            $table->date('fecha_partido');
            $table->integer('goles_a_favor')->nullable();
            $table->integer('goles_en_contra')->nullable();
            $table->string('resultado')->nullable();
            $table->float('actuacion_equipo')->nullable();
            $table->boolean('local')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('matches');
    }
}

