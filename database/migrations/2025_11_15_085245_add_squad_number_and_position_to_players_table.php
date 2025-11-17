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
        Schema::table('players', function (Blueprint $table) {
            $table->smallInteger('squad_number')->nullable();
            $table->unique(['team_id','squad_number']);
            $table->enum('position', ['gk','cb','rb','lb','rwb','lwb','cm','cdm','amf','rm','lm','lwf','rwf','cf','st'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn('squad_number');
            $table->dropColumn('position');
            $table->dropUnique('players_team_id_squad_number_unique');
        });
    }
};
