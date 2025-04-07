<?php

use App\Http\Controllers\Web\WebController;
use App\Services\Contracts\TournamentServiceInterface;
use Illuminate\Support\Facades\Route;

//Main Route That Show Tournament Lists
Route::get('/', [WebController::class, 'index']);

//Management Route That Can Manage Tournament Fixture & Teams
Route::get('/tournament-management/{tournamentId}', [WebController::class, 'management']);

//Show Total Week And Matched
Route::get('/fixtures/{tournamentId}', [WebController::class, 'fixture']);

//Scoreboard
Route::get('/scoreboard/{tournamentId}', [WebController::class, 'scoreboard']);

//Management Route That Can Manage Tournament Fixture & Teams
Route::get('test', function () {

    $teamIds = [1,2,3,4,5,6];
    $fixed = array_shift($teamIds);
    $last = array_pop($teamIds);
    array_unshift($teamIds, $last);
    array_unshift($teamIds, $fixed);

    $fixed = array_shift($teamIds);
    $last = array_pop($teamIds);
    array_unshift($teamIds, $last);
    array_unshift($teamIds, $fixed);

    $ts = app(TournamentServiceInterface::class);
    $ts->generateTournamentGames(tournamentId: 1);

    dd('done');
    /** @var \App\Services\Contracts\ScoreboardServiceInterface $scSer */
    $scSer = app(\App\Services\Contracts\ScoreboardServiceInterface::class);

    $scb = \App\Models\Scoreboard::query()->orderBy("rank")->get();

    $tournament = \App\Models\Tournament::query()->first();
    $weekLeft = $tournament->total_weeks - $tournament->current_week + 1;
    dd($weekLeft);
    return $scSer->calculateChampionshipPrediction($scb,2);
});