<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Scoreboard;
use App\Models\Tournament;
use App\Services\Contracts\ScoreboardServiceInterface;
use App\Services\Contracts\TeamServiceInterface;
use App\Services\Contracts\TournamentServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class ScoreboardService implements ScoreboardServiceInterface
{
    public function __construct(
        protected TournamentServiceInterface $tournamentService,
        protected TeamServiceInterface $teamService
    ) {
    }

    /** @inheritDoc */
    public function getScoreboardsByTournament(int $tournamentId, array $relations = []): Collection
    {
        return Scoreboard::query()->with($relations)->where("tournament_id", $tournamentId)->orderBy("rank")->get();
    }

    /** @inheritDoc */
    public function getScoreboardByTeamAndTournament(int $teamId, int $tournamentId, array $relations = []): Scoreboard
    {
        $attribute = [
            'team_id' => $teamId,
            'tournament_id' => $tournamentId
        ];
        return Scoreboard::query()->with($relations)->firstOrCreate($attribute);
    }

    /** @inheritDoc */
    public function calculateScoreboard(int $teamId, int $tournamentId): void
    {
        $scoreboard = $this->getScoreboardByTeamAndTournament(
            teamId: $teamId,
            tournamentId: $tournamentId,
            relations: [
                'team.homeGames' => fn($q) => $q->where('tournament_id', $tournamentId),
                'team.awayGames' => fn($q) => $q->where('tournament_id', $tournamentId),
            ]
        );

        $homeGames = $scoreboard->team->homeGames->map(function ($g) {
            $g->setAttribute('is_home', true);
            return $g;
        });
        $awayGames = $scoreboard->team->awayGames->map(function ($g) {
            $g->setAttribute('is_home', false);
            return $g;
        });;

        $allGames = $homeGames->map(fn($g) => $g)
            ->merge($awayGames->map(fn($g) => $g))
            ->where('played', true);

        $playedGame = $allGames->count();

        $winGames = $allGames->filter(fn($g) => ($g->is_home && $g->home_goals > $g->away_goals) ||
            (!$g->is_home && $g->away_goals > $g->home_goals)
        )->count();

        $drawGames = $allGames->filter(fn($g) => $g->home_goals == $g->away_goals
        )->count();

        $loseGames = $allGames->filter(fn($g) => ($g->is_home && $g->home_goals < $g->away_goals) ||
            (!$g->is_home && $g->away_goals < $g->home_goals)
        )->count();

        $goalsFor = $homeGames->sum("home_goals") + $awayGames->sum("away_goals");

        $goalsAgainst = $homeGames->sum("away_goals") + $awayGames->sum("home_goals");

        $scoreboard->points = ($winGames * 3) + ($drawGames);
        $scoreboard->games_played = $playedGame;
        $scoreboard->game_win = $winGames;
        $scoreboard->game_lose = $loseGames;
        $scoreboard->game_draw = $drawGames;
        $scoreboard->goals_for = $goalsFor;
        $scoreboard->goals_against = $goalsAgainst;
        $scoreboard->goals_different = $goalsFor - $goalsAgainst;
        $scoreboard->save();
    }

    /** @inheritDoc */
    public function calculateRankings(int $tournamentId, bool $updateBoard = false): Collection
    {
        $scoreboards = $this->getScoreboardsByTournament($tournamentId);

        /** @var Tournament $tournament */
        $tournament = $this->tournamentService->show($tournamentId);

        $scoreboards = $scoreboards->sortByDesc(function ($scoreboard) {
            return [
                $scoreboard->points,
                $scoreboard->goals_different,
                $scoreboard->goals_for
            ];
        });


        $predictions = [];
        $weeksLeft = $tournament->total_weeks - $tournament->current_week + 1;
        if ($weeksLeft <= 3 and !$tournament->completed) {
            $predictions = $this->calculateChampionshipPrediction(
                rankedScoreboard: $scoreboards,
                weeksLeft: $weeksLeft,
            );
        }

        $rank = 1;
        /** @var Scoreboard $scoreboard */
        foreach ($scoreboards as $scoreboard) {
            $scoreboard->rank = $rank;
            if ($tournament->completed) {
                if ($rank == 1) {
                    $scoreboard->champion_prediction = 100;
                } else {
                    $scoreboard->champion_prediction = 0;
                }
            } else {
                if (!empty($predictions)) {
                    $scoreboard->champion_prediction = $predictions[$scoreboard->id] ?? 0;
                }
            }

            if ($updateBoard) {
                $scoreboard->save();
            }
            $rank++;
        }

        return $scoreboards;
    }


    public function calculateChampionshipPrediction(
        Collection $rankedScoreboard,
        $weeksLeft
    ): array {
        /**
         * @note (explain Scenario)
         * first we will sort scoreboards with ranks
         * then calculate maximum possible point can earn by each team
         * after that we make first rank team point as ruler
         * in loop we calculate maximum possible point can earn by each team based on current point
         * if maxPotentialPoints is less than Leader Point So Chance is zero
         * else then try to calculate point gap between team and leader based on each point we minus calculated point value from 100
         * ( this number is related to  weeks left , if different is more than 9 is 3 weeks left so basically chance is zero coz
         *   a team can earn maximum 9 points in 3 weeks)
         *  point value formula =>  if 3 week left  so maximum point will be 9  and 10 is first point that reach to zero chance
         *
         * so 1 week left point value is = 1*100 / 4 => 25
         * so 2 week left point value is = 1*100 / 7 => 14.28...
         * so 3 week left point value is = 1*100 / 10 => 10
         * so ...
         *
         * each team will earn a number between 0 - 100
         * based on total point earned by teams we normalize percentage to be sure always total will be 100
         *
         *  ------- improvement -----
         *  as I research there is lots of way like `monte carlo simulation` algorithm that can run simulation for like
         *  N times for a scenario and based on that can decide better and  more specific result , I just in simple way
         */


        //sort scoreboards
        $scoreboards = $rankedScoreboard->sortBy("rank");

        //calculate maximum possible
        $maxPointsPossible = $weeksLeft * 3;

        //first rank team point as ruler
        $leader = $scoreboards->first();
        $leaderPoints = $leader->points;


        //calculate point value based on weeks left
        $pointValue = $weeksLeft * 3 + 1;
        $pointValue = 100 / $pointValue;

        $predictions = [];
        foreach ($scoreboards as $scoreboard) {
            $scoreboardId = $scoreboard->id;
            $currentPoints = $scoreboard->points;
            $maxPotentialPoints = $currentPoints + $maxPointsPossible;

            $pointGap = $leaderPoints - $currentPoints;

            if ($maxPotentialPoints < $leaderPoints) {
                $predictions[$scoreboardId] = 0;
            } else {
                $predictions[$scoreboardId] = max(0, 100 - ($pointGap * $pointValue));
            }
        }

        //normalize percentage
        $total = array_sum($predictions);
        if ($total > 0) {
            foreach ($predictions as $scoreboardId => $percentage) {
                $predictions[$scoreboardId] = round(($percentage / $total) * 100);
            }
        }


        $total = array_sum($predictions);
        if ($total != 100) {
            $predictions[$leader->id] = $predictions[$leader->id] + (100 - $total);
        }

        return $predictions;
    }

}