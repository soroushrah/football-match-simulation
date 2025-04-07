<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Tournament;
use App\Services\Contracts\GameServiceInterface;
use App\Services\Contracts\ScoreboardServiceInterface;
use App\Services\Contracts\TournamentServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GameService implements GameServiceInterface
{

    public function __construct(
        protected ScoreboardServiceInterface $scoreboardService,
        protected TournamentServiceInterface $tournamentService,
    ) {
    }

    /** @inheritDoc
     * @param int $tournamentId
     * @param array $relations
     * @param bool $keyByWeek
     */
    public function getGamesByTournamentId(
        int $tournamentId,
        array $relations = [],
        string|null $groupBy = null
    ): Collection {
        $games = Game::query()->where('tournament_id', $tournamentId)->with($relations)->orderBy("week")->get();
        return $groupBy ? $games->groupBy($groupBy) : $games;
    }

    /** @inheritDoc */
    public function getGamesByTournamentIdAndWeek(int $tournamentId, int $week, array $relations = []): Collection
    {
        return Game::query()->where('tournament_id', $tournamentId)->where('week', $week)->with($relations)->get();
    }

    /** @inheritDoc */
    public function getGamesByTournamentIdAndCurrentWeek(int $tournamentId, array $relations = []): Collection
    {
        /** @var Tournament $tournament */
        $tournament = $this->tournamentService->show($tournamentId);
        return Game::query()->where('tournament_id', $tournamentId)->where('week', $tournament->current_week)->with(
            $relations
        )->get();
    }

    /** @inheritDoc */
    public function getGameById(int $gameId, array $relations = []): Game
    {
        return Game::query()->with($relations)->findOrFail($gameId);
    }

    /** @inheritDoc */
    public function playGamesOfWeek(int|Tournament $tournament, int $week): void
    {

        /** @var Tournament $tournament */
        if (!$tournament instanceof Tournament) {
            $tournament = $this->tournamentService->show($tournament);
        }

        if ($tournament->completed) {
            throw ValidationException::withMessages([
                "tournament" => __("validation.exceptions.tournament.already_held"),
            ]);
        }

        if ($week < $tournament->current_week) {
            throw ValidationException::withMessages([
                "tournament" => __("validation.exceptions.tournament.week_held"),
            ]);
        }

        $games = $this->getGamesByTournamentIdAndWeek(tournamentId: $tournament->id, week: $week, relations: [
            "homeTeam",
            "awayTeam"
        ]);

        DB::beginTransaction();
        try {
            /** @var Game $game */
            foreach ($games as $game) {
                $this->playGame($game);
            }

            $currentWeek = $week + 1;
            $tournament->update([
                "current_week" => $currentWeek > $tournament->total_weeks ? $tournament->total_weeks : $currentWeek,
                "completed" => $currentWeek > $tournament->total_weeks,
            ]);
            $this->scoreboardService->calculateRankings(tournamentId: $tournament->id, updateBoard: true);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /** @inheritDoc */
    public function playGamesOfCurrentWeek(int $tournamentId): void
    {
        /** @var Tournament $tournament */
        $tournament = $this->tournamentService->show($tournamentId);
        $this->playGamesOfWeek(tournament: $tournament, week: $tournament->current_week);
    }

    /** @inheritDoc */
    public function playAllGamesOfTournament(int $tournamentId): void
    {
        /** @var Tournament $tournament */
        $tournament = $this->tournamentService->show($tournamentId);
        if ($tournament->completed) {
            throw ValidationException::withMessages([
                "tournament" => __("validation.exceptions.tournament.already_held"),
            ]);
        }

        $games = $this->getGamesByTournamentId(tournamentId: $tournamentId, relations: [
            "homeTeam",
            "awayTeam"
        ]);

        DB::beginTransaction();
        try {
            foreach ($games as $game) {
                $this->playGame($game);
            }

            $tournament->update([
                "current_week" => $tournament->total_weeks,
                "completed" => true,
            ]);

            $this->scoreboardService->calculateRankings(tournamentId: $tournament->id, updateBoard: true);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }


    /**
     * @param Game $game
     * @return void
     */
    private function playGame(Game $game): void
    {
        $homeTeam = $game->homeTeam;
        $awayTeam = $game->awayTeam;

        $game->home_goals = $this->generateGoalBasedOnPower(
            attackPower: $homeTeam->power,
            defensePower: $awayTeam->power
        );
        $game->away_goals = $this->generateGoalBasedOnPower(
            attackPower: $awayTeam->power,
            defensePower: $homeTeam->power
        );
        $game->played = true;
        $game->save();

        $this->scoreboardService->calculateScoreboard(teamId: $homeTeam->id, tournamentId: $game->tournament_id);
        $this->scoreboardService->calculateScoreboard(teamId: $awayTeam->id, tournamentId: $game->tournament_id);
    }

    /**
     * @param int $attackPower
     * @param int $defensePower
     * @return int
     */
    private function generateGoalBasedOnPower(int $attackPower, int $defensePower): int
    {
        // Avoid Divided By Zero
        $attackPower = max(1, $attackPower);
        $defensePower = max(1, $defensePower);

        // Calculate Number Based On Power
        // Higher Power Achieve Higher Number Between (0 to 1)
        $effectedNumber = $attackPower / ($attackPower + $defensePower);
        $maximumAllowedGoal = 10;

        $maxGoals = (int)round($effectedNumber * $maximumAllowedGoal);

        return rand(0, $maxGoals == 0 ? 1 : $maxGoals);
    }


}