<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('team_tournament', function (Blueprint $table) {
            $table->foreignId("team_id")->constrained("teams")->cascadeOnDelete();
            $table->foreignId("tournament_id")->constrained("tournaments")->cascadeOnDelete();
            $table->timestamp("created_at")->useCurrent();

            $table->primary(["team_id", "tournament_id"]);
            $table->unique(["team_id", "tournament_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_tournament');
    }
};
