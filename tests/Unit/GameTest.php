<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\Contracts\GameServiceInterface;
use App\Services\Contracts\TournamentServiceInterface;
use Illuminate\Support\Str;
use Tests\TestCase;

class GameTest extends TestCase
{
    private GameServiceInterface $gameService;

    private Tournament $tournament;

    private int $teamCount = 4;

    protected function setUp(): void
    {
        parent::setUp();

        $tournamentService = app(TournamentServiceInterface::class);

        $this->gameService = app(GameServiceInterface::class);

        $tournament = $tournamentService->storeTournamentWithRandomTeams(
            name: Str::random(5),
            teamCount: $this->teamCount,
        );

        $tournamentService->generateTournamentGames(
            tournamentId: $tournament->id,
        );
        $this->tournament = $tournament;
    }

    public function test_getGamesByTournamentId()
    {
        $games = $this->gameService->getGamesByTournamentId($this->tournament->id);

        $expectedGameCount = (($this->teamCount - 1) * 2) * ($this->teamCount / 2);

        $this->assertEquals($expectedGameCount, $games->count());
    }

    public function test_playGamesOfWeek()
    {
        $this->gameService->playGamesOfWeek(
            tournament: $this->tournament->id,
            week: 1,
        );

        $games = $this->gameService->getGamesByTournamentId($this->tournament->id);

        $this->assertEquals($games->where("played", 1)->count(), ($this->teamCount / 2));
    }

    public function test_playAllGamesOfTournament()
    {
        $this->gameService->playAllGamesOfTournament(
            tournamentId: $this->tournament->id,
        );
        $expectedGameCount = (($this->teamCount - 1) * 2) * ($this->teamCount / 2);

        $games = $this->gameService->getGamesByTournamentId($this->tournament->id);

        $this->assertEquals($games->where("played", 1)->count(), $expectedGameCount);
    }

    public function tearDown(): void
    {
        Tournament::withTrashed()->forceDelete();
        Team::withTrashed()->forceDelete();
        Game::query()->delete();
        parent::tearDown();
    }
}
