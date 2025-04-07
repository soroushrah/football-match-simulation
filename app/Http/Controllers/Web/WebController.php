<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Contracts\TournamentServiceInterface;
use Illuminate\View\View;

class WebController extends Controller
{
    public function __construct(protected TournamentServiceInterface $tournamentService)
    {
    }

    /**
     * Display the tournament application view.
     *
     * @return View
     */
    public function index(): View
    {
        return view('tournament.index');
    }

    public function management(int $tournamentId): View
    {
        $tournament = $this->tournamentService->show($tournamentId);
        return view('tournament.management',compact('tournament'));
    }

    /**
     * @return View
     */
    public function fixture(int $tournamentId): View
    {
        $tournament = $this->tournamentService->show($tournamentId);
        return view('game.fixture',compact("tournament"));
    }

    /**
     * @return View
     */
    public function scoreboard(int $tournamentId): View
    {
        $tournament = $this->tournamentService->show($tournamentId);
        return view('scoreboard.scoreboard',compact("tournament"));
    }
}