<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Models\Scoreboard;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\Contracts\GameServiceInterface;
use App\Services\Contracts\ScoreboardServiceInterface;
use App\Services\Contracts\TeamServiceInterface;
use App\Services\Contracts\TournamentServiceInterface;
use Illuminate\Support\Str;
use Tests\TestCase;

class ScoreboardTest extends TestCase
{
    private ScoreboardServiceInterface $scoreboardService;

    private Tournament $tournament;

    private int $teamCount = 4;

    protected function setUp(): void
    {
        parent::setUp();

        $tournamentService = app(TournamentServiceInterface::class);

        $gameService = app(GameServiceInterface::class);

        $this->scoreboardService = app(ScoreboardServiceInterface::class);

        $tournament = $tournamentService->storeTournamentWithRandomTeams(
            name: Str::random(5),
            teamCount: $this->teamCount,
        );

        $tournamentService->generateTournamentGames(
            tournamentId: $tournament->id,
        );

        $gameService->playAllGamesOfTournament($tournament->id);

        $this->tournament = $tournament->fresh();
    }

    public function test_get_scoreboards_by_tournament()
    {
        $scoreboards = $this->scoreboardService->getScoreboardsByTournament(
            tournamentId: $this->tournament->id,
        );
        $this->assertCount($this->teamCount, $scoreboards);
    }

    public function test_get_scoreboard_by_team_and_tournament()
    {
        $this->tournament->loadMissing("teams");

        $team = $this->tournament->teams->random();
        $scoreboard = $this->scoreboardService->getScoreboardByTeamAndTournament(
            teamId: $team->id,
            tournamentId: $this->tournament->id,
        );

        $this->assertEquals($team->id, $scoreboard->team_id);
        $this->assertEquals($this->tournament->id, $scoreboard->tournament_id);
    }


    public function test_calculate_scoreboard()
    {
        $this->tournament->loadMissing("teams");

        $team = $this->tournament->teams->random();
        $this->scoreboardService->calculateScoreboard(
            teamId: $team->id,
            tournamentId: $this->tournament->id,
        );

        $scoreboard = $this->scoreboardService->getScoreboardByTeamAndTournament(
            teamId: $team->id,
            tournamentId: $this->tournament->id,
        );

       $this->assertEquals(($this->teamCount - 1) * 2, $scoreboard->games_played);
    }


    public function tearDown(): void
    {
        Tournament::withTrashed()->forceDelete();
        Team::withTrashed()->forceDelete();
        Game::query()->delete();
        parent::tearDown();
    }
}
