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
        Schema::create('scoreboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId("tournament_id")->constrained("tournaments")->cascadeOnDelete();
            $table->foreignId("team_id")->constrained("teams")->cascadeOnDelete();
            $table->unsignedMediumInteger("points")->default(0);
            $table->unsignedMediumInteger("goals_for")->default(0);
            $table->unsignedMediumInteger("goals_against")->default(0);
            $table->mediumInteger("goals_different")->default(0);
            $table->unsignedMediumInteger("game_win")->default(0);
            $table->unsignedMediumInteger("game_draw")->default(0);
            $table->unsignedMediumInteger("game_lose")->default(0);
            $table->unsignedMediumInteger("games_played")->default(0);
            $table->unsignedMediumInteger("champion_prediction")->default(0);
            $table->unsignedMediumInteger("rank")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scoreboards');
    }
};
