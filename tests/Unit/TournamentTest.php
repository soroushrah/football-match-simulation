<?php

namespace Tests\Unit;

use App\DTOs\Tournament\TournamentStoreDTO;
use App\Models\Tournament;
use App\Services\Contracts\TournamentServiceInterface;
use Illuminate\Support\Str;
use Tests\TestCase;

class TournamentTest extends TestCase
{

    private TournamentServiceInterface $tournamentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tournamentService = app(TournamentServiceInterface::class);
    }

    public function test_create_tournament(): void
    {
        $name = Str::random(5);

        $tournament = $this->tournamentService->store(
            DTO: new TournamentStoreDTO(
                name: $name,
            ),
        );

        /** @var Tournament $fetchTournament */
        $fetchTournament = $this->tournamentService->show(
            id: $tournament->id
        );

        $this->assertNotNull($fetchTournament);
        $this->assertEquals($name, $fetchTournament->name);
    }

    public function test_show_tournament(): void
    {
        $name = Str::random(5);

        $dto = new TournamentStoreDTO(name: $name);

        $tournament = $this->tournamentService->store(DTO: $dto);

        $fetchedTournament = $this->tournamentService->show(id: $tournament->id);


        $this->assertEquals($tournament->name, $fetchedTournament->name);
        $this->assertEquals($tournament->id, $fetchedTournament->id);
    }

    public function test_destroy_tournament(): void
    {
        $name = Str::random(5);

        $dto = new TournamentStoreDTO(name: $name);

        $tournament = $this->tournamentService->store(DTO: $dto);

        $this->tournamentService->delete(id: $tournament->id, force: true);

        $this->assertNull(Tournament::withTrashed()->find($tournament->id));
    }

    public function test_generate_tournament_games()
    {
        $name = Str::random(5);

        $teamCountChoice = [4, 6, 8, 10, 12, 14, 16];
        $teamCount = $teamCountChoice[array_rand($teamCountChoice)];

        /** @var Tournament $tournament */
        $tournament = $this->tournamentService->storeTournamentWithRandomTeams(
            name: $name,
            teamCount: $teamCount,
        );

        $this->tournamentService->generateTournamentGames(tournamentId: $tournament->id);

        $weeksPerRound = $teamCount - 1;
        $totalWeeks = $weeksPerRound * 2;
        $gamesPerWeek = $teamCount / 2;

        $expectedTotalGames = $gamesPerWeek * $totalWeeks;

        $gamesCount = $tournament->games()->count();

        $this->assertEquals($expectedTotalGames, $gamesCount);

        $this->assertEquals($teamCount, $tournament->teams()->count());
    }

    public function tearDown(): void
    {
        Tournament::withTrashed()->forceDelete();
        parent::tearDown();
    }
}
