<?php

use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\ScoreboardController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TournamentController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix"=>"tournaments"], function () {
    Route::post('/generate-games/{id}', [TournamentController::class, 'generateTournamentGames']);
    Route::post('/re-generate-games/{id}', [TournamentController::class, 'reGenerateTournamentGames']);
    Route::apiResource('', TournamentController::class)->parameter('', 'id')->except(['update']);
});

Route::group(["prefix"=>"teams"], function () {
    Route::apiResource('', TeamController::class)->parameter('', 'id');
});

Route::group(["prefix"=>"games"], function () {
    Route::get('get-by-tournament/{id}', [GameController::class, 'getByTournament']);
    Route::get('get-tournament-current-week/{id}', [GameController::class, 'getTournamentCurrentWeek']);

    Route::post('play-games-of-current-week/{id}', [GameController::class, 'playGamesOfCurrentWeek']);
    Route::post('play-all-games-of-tournament/{id}', [GameController::class, 'playAllGamesOfTournament']);
});

Route::group(["prefix"=>"scoreboards"], function () {
    Route::get('get-by-tournament/{id}', [ScoreboardController::class, 'getByTournament']);
});