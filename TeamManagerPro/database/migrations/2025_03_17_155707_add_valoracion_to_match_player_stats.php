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
    Schema::table('match_player_stats', function (Blueprint $table) {
        $table->decimal('valoracion', 4, 2)->nullable()->after('tarjetas_rojas'); // Permite valores decimales
    });
}

public function down()
{
    Schema::table('match_player_stats', function (Blueprint $table) {
        $table->dropColumn('valoracion');
    });
}

};
