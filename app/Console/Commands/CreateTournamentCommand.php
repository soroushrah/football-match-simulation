<?php

namespace App\Console\Commands;

use App\Services\Contracts\TournamentServiceInterface;
use Illuminate\Console\Command;

class CreateTournamentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-tournament {--team-count= : The number of teams} {--name= : The name of the team}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This Command Is For Creating Tournament And Tournament Teams 
    - 'team-count' Option Is For Number Of Tournament Teams And 'Name' Is For Tournament Name";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $teamCount = $this->option('team-count');
        $name = $this->option('name');

        if ($teamCount < 2 || $teamCount % 2 != 0) {
            echo "Team Counts Must Be Even\n";
            return;
        }

        if ($teamCount > 64) {
            echo "Team Counts Limit Is 64\n";
            return;
        }

        if ($name === null) {
            echo "Name Option is required\n";
            return;
        }

        /** @var TournamentServiceInterface $tournamentService */
        $tournamentService = app(TournamentServiceInterface::class);
        $tournamentService->storeTournamentWithRandomTeams(
            name: $name,
            teamCount: $teamCount,
        );
        echo "Tournament {$name} stored With Including {$teamCount} Teams\n";
    }
}
