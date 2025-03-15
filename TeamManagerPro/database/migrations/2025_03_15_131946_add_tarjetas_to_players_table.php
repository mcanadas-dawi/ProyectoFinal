<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::table('players', function (Blueprint $table) {
        $table->integer('tarjetas_amarillas')->default(0)->after('valoracion');
        $table->integer('tarjetas_rojas')->default(0)->after('tarjetas_amarillas');
    });
}

public function down(): void {
    Schema::table('players', function (Blueprint $table) {
        $table->dropColumn('tarjetas_amarillas');
        $table->dropColumn('tarjetas_rojas');
        });
    }
};
