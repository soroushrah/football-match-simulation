<?php

namespace App\Services;

use App\DTOs\Team\TeamStoreDTO;
use App\DTOs\Tournament\TournamentStoreDTO;
use App\Models\Game;
use App\Models\Scoreboard;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\Contracts\TeamServiceInterface;
use App\Services\Contracts\TournamentServiceInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TournamentService implements TournamentServiceInterface
{

    public function __construct(
        protected TeamServiceInterface $teamService
    ) {
    }

    /** @inheritDoc */
    public function all(
        array $filters = [],
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator {
        $query = Tournament::query()->with($relations);
        return $pagination ? $query->paginate($pagination) : $query->get();
    }

    /** @inheritDoc */
    public function show(int $id, array $relations = []): Model
    {
        return Tournament::query()->with($relations)->findOrFail($id);
    }

    /** @inheritDoc */
    public function store(TournamentStoreDTO $DTO): Model
    {
        return Tournament::storeByDTO($DTO);
    }

    /** @inheritDoc */
    public function generateTournamentGames(int $tournamentId, bool $regenerate = false): void
    {
        /** @var Tournament $tournament */
        $tournament = $this->show(
            id: $tournamentId,
            relations: ["teams", "games"]
        );

        // Avoid ReGenerate Tournament Games Until Requested
        if (!$regenerate and $tournament->games->count() > 0) {
            throw ValidationException::withMessages([
                "games" => __("validation.exceptions.tournament.games_already_generated"),
            ]);
        }

        /** @var Collection<Team> $registeredTeams */
        $registeredTeams = $tournament->teams;
        $teamCount = $registeredTeams->count();

        // Make Sure The Tournament Will Begin With Even Registered Teams
        if ($teamCount % 2 != 0) {
            throw ValidationException::withMessages([
                "teams" => __("validation.exceptions.tournament.even_team_registered"),
            ]);
        }

        /* @note (Explain The Scenario)
         *
         * If Total Team Be 6 Then 3 Week Will Be First Round (Season) And 3 Week Be Second Round
         * We Should Be Sure If Team A Play With B In First Round (First 3 Week) And
         * Team A Is Home Team And Team Be Is Away Team Then Reverse Match Must Be Held In Second Round (Second 3 Week)
         * So We Create First Round Match As Unique
         *
         */
        $weeksPerRound = $registeredTeams->count() - 1;
        $totalWeeks = $weeksPerRound * 2;
        $schedule = [];
        $teamIds = $registeredTeams->pluck('id')->toArray();

        for ($week = 0; $week < $weeksPerRound; $week++) {
            $roundGames = [];

            for ($i = 0; $i < $teamCount / 2; $i++) {
                $home = $teamIds[$i];
                $away = $teamIds[$teamCount - 1 - $i];

                $roundGames[] = [$home, $away];
            }

            $schedule[$week + 1] = $roundGames;

            $fixed = array_shift($teamIds);
            $last = array_pop($teamIds);
            array_unshift($teamIds, $last);
            array_unshift($teamIds, $fixed);
        }

        //Second round (reverse home/away)
        for ($week = 1; $week <= $weeksPerRound; $week++) {
            $reversedGames = array_map(fn($game) => array_reverse($game), $schedule[$week]);
            $schedule[$weeksPerRound + $week] = $reversedGames;
        }


        $finalGames = [];
        foreach ($schedule as $week => $games) {
            foreach ($games as $game) {
                $finalGames[] = [
                    "tournament_id" => $tournamentId,
                    "home_team_id" => $game[0],
                    "away_team_id" => $game[1],
                    "week" => $week,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                ];
            }
        }

        Game::query()->where("tournament_id", $tournamentId)->delete();
        Game::query()->insert($finalGames);

        $tournament->update([
            "total_weeks" => $totalWeeks,
            "current_week" => 1,
            "completed" => false
        ]);

        $scoreboards = [];
        foreach ($tournament->teams as $team) {
            $scoreboards[] = [
                'team_id' => $team->id,
                'tournament_id' => $tournamentId,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ];
        }

        Scoreboard::query()->where("tournament_id", $tournamentId)->delete();
        Scoreboard::query()->insert($scoreboards);
    }

    /** @inheritDoc */
    public function storeTournamentWithRandomTeams(string $name, int $teamCount = 4): Tournament
    {
        //An Array That Contain 64 Football Team Name
        $FOOTBALL_TEAMS = [
            "Liverpool",
            "Real Madrid",
            "Barcelona",
            "Manchester United",
            "Bayern Munich",
            "Paris Saint-Germain",
            "Chelsea",
            "Juventus",
            "AC Milan",
            "Inter Milan",
            "Arsenal",
            "Manchester City",
            "Tottenham Hotspur",
            "Borussia Dortmund",
            "Atletico Madrid",
            "AS Roma",
            "FC Porto",
            "Ajax",
            "Benfica",
            "Olympique Lyonnais",
            "Villarreal",
            "Sevilla",
            "Bayer Leverkusen",
            "RB Leipzig",
            "Everton",
            "Leicester City",
            "Napoli",
            "Lazio",
            "West Ham United",
            "Southampton",
            "Wolverhampton Wanderers",
            "ACF Fiorentina",
            "Valencia",
            "Galatasaray",
            "Celtic",
            "Rangers",
            "Sunderland",
            "Newcastle United",
            "Real Sociedad",
            "Athletic Bilbao",
            "Lyon",
            "Shakhtar Donetsk",
            "Zenit St. Petersburg",
            "Fenerbahce",
            "Besiktas",
            "Al Hilal",
            "Al Nassr",
            "Shanghai SIPG",
            "Beijing Guoan",
            "Melbourne Victory",
            "Perth Glory",
            "Club America",
            "Chivas Guadalajara",
            "Boca Juniors",
            "River Plate",
            "Sao Paulo",
            "Flamengo",
            "Corinthians",
            "Palmeiras",
            "Independiente",
            "Velez Sarsfield",
            "Deportivo La Coruna",
            "Real Betis",
            "Club Brugge",
            "Anderlecht",
            "Dynamo Kyiv",
            "CSKA Moscow",
            "Sampdoria",
            "Aston Villa",
            "Fulham",
            "Nottingham Forest",
            "Bristol City",
            "RC Lens"
        ];

        // 1 - Fresh DB
        Artisan::call("migrate:fresh --force");
        // 2 - Create Tournament
        $tournamentStoreDTO = new TournamentStoreDTO(
            name: $name,
        );

        DB::beginTransaction();
        try {
            /** @var Tournament $tournament */
            $tournament = $this->store($tournamentStoreDTO);

            // 3 - Create Teams Based On Teams Count
            $footballTeamNamesIndex = array_rand($FOOTBALL_TEAMS, $teamCount);
            foreach ($footballTeamNamesIndex as $index) {
                $teamStoreDTO = new TeamStoreDTO(
                    name: $FOOTBALL_TEAMS[$index],
                    power: rand(1, 100),
                );
                /** @var Team $team */
                $team = $this->teamService->store($teamStoreDTO);
                $this->teamService->registerTeamInTournament(
                    teamId: $team->id,
                    tournamentId: $tournament->id,
                );
            }

            DB::commit();
            return $tournament;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }


    /** @inheritDoc */
    public function delete(int $id, bool $force = false): bool
    {
        $tournament = $this->show($id);
        return $force ? $tournament->forceDelete() : $tournament->delete();
    }
}