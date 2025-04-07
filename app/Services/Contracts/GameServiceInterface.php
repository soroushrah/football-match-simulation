<?php

namespace App\Services\Contracts;

use App\Models\Game;
use App\Models\Tournament;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

interface GameServiceInterface
{
    /**
     * @param int $tournamentId
     * @param array $relations
     * @param string|null $groupBy
     * @return Collection
     */
    public function getGamesByTournamentId(
        int $tournamentId,
        array $relations = [],
        string|null $groupBy = null
    ): Collection;

    /**
     * @param int $tournamentId
     * @param int $week
     * @param array $relations
     * @return Collection
     */
    public function getGamesByTournamentIdAndWeek(int $tournamentId, int $week, array $relations = []): Collection;

    /**
     * @param int $tournamentId
     * @param array $relations
     * @return Collection
     */
    public function getGamesByTournamentIdAndCurrentWeek(int $tournamentId, array $relations = []): Collection;

    /**
     * @param int $gameId
     * @param array $relations
     * @return Game
     */
    public function getGameById(int $gameId, array $relations = []): Game;


    /**
     * @param int|Tournament $tournament
     * @param int $week
     * @return void
     * @throws ValidationException
     * @throws Exception
     */
    public function playGamesOfWeek(int|Tournament $tournament, int $week): void;

    /**
     * @param int $tournamentId
     * @return void
     * @throws ValidationException
     * @throws Exception
     */
    public function playGamesOfCurrentWeek(int $tournamentId): void;

    /**
     * @param int $tournamentId
     * @return void
     * @throws ValidationException
     * @throws Exception
     */
    public function playAllGamesOfTournament(int $tournamentId): void;


}