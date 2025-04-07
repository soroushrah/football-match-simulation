<?php

namespace Tests\Unit;

use App\DTOs\Team\TeamStoreDTO;
use App\DTOs\Team\TeamUpdateDTO;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\Contracts\TeamServiceInterface;
use Illuminate\Support\Str;
use Tests\TestCase;

class TeamTest extends TestCase
{
    private TeamServiceInterface $teamService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teamService = app(TeamServiceInterface::class);
    }

    public function test_store_team()
    {
        $name = Str::random(5);
        $power = rand(1, 100);

        $team = $this->teamService->store(
            DTO: new TeamStoreDTO(
                name: $name,
                power: $power
            )
        );

        $this->assertInstanceOf(Team::class, $team);
        $this->assertNotNull($team->id);
    }

    public function test_show_team()
    {
        $name = Str::random(5);
        $power = rand(1, 100);

        $team = $this->teamService->store(
            DTO: new TeamStoreDTO(
                name: $name,
                power: $power
            )
        );

        /** @var Team $team */
        $team = $this->teamService->show(
            id: $team->id,
        );

        $this->assertEquals($name, $team->name);
        $this->assertEquals($power, $team->power);
    }

    public function test_update_team()
    {
        $name = Str::random(5);
        $power = rand(1, 50);

        $team = $this->teamService->store(
            DTO: new TeamStoreDTO(
                name: $name,
                power: $power
            )
        );

        /** @var Team $team */
        $team = $this->teamService->show(
            id: $team->id,
        );

        $this->assertEquals($name, $team->name);
        $this->assertEquals($power, $team->power);

        $newName = Str::random(6);
        $newPower = rand(51, 100);

        $team = $this->teamService->update(
            DTO: new TeamUpdateDTO(
                id: $team->id,
                name: $newName,
                power: $newPower
            )
        );

        /** @var Team $team */
        $team = $this->teamService->show(
            id: $team->id,
        );

        $this->assertNotEquals($name, $team->name);
        $this->assertNotEquals($power, $team->power);

        $this->assertEquals($newName, $team->name);
        $this->assertEquals($newPower, $team->power);
    }

    public function test_delete_team()
    {
        $name = Str::random(5);
        $power = rand(1, 100);

        $team = $this->teamService->store(
            DTO: new TeamStoreDTO(
                name: $name,
                power: $power
            )
        );

        /** @var Team $team */
        $team = $this->teamService->show(
            id: $team->id,
        );

        $this->assertEquals($name, $team->name);
        $this->assertEquals($power, $team->power);

        $this->teamService->delete(id: $team->id, force: true);

        $this->assertNull(Team::withTrashed()->find($team->id));
    }

    public function test_register_team_in_tournament()
    {
        $tournament = Tournament::factory()->create();

        $name = Str::random(5);
        $power = rand(1, 100);

        $team = $this->teamService->store(
            DTO: new TeamStoreDTO(
                name: $name,
                power: $power
            )
        );

        $this->assertInstanceOf(Team::class, $team);
        $this->assertNotNull($team->id);

        $this->teamService->registerTeamInTournament(
            teamId: $team->id,
            tournamentId: $tournament->id,
        );

        $team = $team->refresh();

        $teamTournament = $team->tournaments()->first();

        $this->assertNotNull($teamTournament);

        $this->assertEquals($tournament->id, $teamTournament->id);

    }


    public function tearDown(): void
    {
        Tournament::withTrashed()->forceDelete();
        Team::withTrashed()->forceDelete();
        parent::tearDown();
    }
}
