<?php

namespace App\Services\Contracts;

use App\Models\Scoreboard;
use Illuminate\Database\Eloquent\Collection;

interface ScoreboardServiceInterface
{
    /**
     * @param int $tournamentId
     * @param array $relations
     * @return Collection
     */
    public function getScoreboardsByTournament(int $tournamentId, array $relations = []): Collection;

    /**
     * @param int $teamId
     * @param int $tournamentId
     * @return Scoreboard
     */
    public function getScoreboardByTeamAndTournament(int $teamId, int $tournamentId): Scoreboard;

    /**
     * @param int $teamId
     * @param int $tournamentId
     * @return void
     */
    public function calculateScoreboard(int $teamId, int $tournamentId): void;

    /**
     * @param int $tournamentId
     * @param bool $updateBoard
     * @return Collection
     */
    public function calculateRankings(int $tournamentId, bool $updateBoard = false): Collection;


}