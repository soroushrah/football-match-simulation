<?php

namespace App\Providers;

use App\Services\Contracts\GameServiceInterface;
use App\Services\Contracts\ScoreboardServiceInterface;
use App\Services\Contracts\TeamServiceInterface;
use App\Services\Contracts\TournamentServiceInterface;
use App\Services\GameService;
use App\Services\ScoreboardService;
use App\Services\TeamService;
use App\Services\TournamentService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{


    public array $singletons = [
        GameServiceInterface::class => GameService::class,
        ScoreboardServiceInterface::class => ScoreboardService::class,
        TeamServiceInterface::class => TeamService::class,
        TournamentServiceInterface::class => TournamentService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
