<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameResources;
use App\Http\Resources\SuccessResponse;
use App\Services\Contracts\GameServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class GameController extends Controller
{
    /**
     * @param GameServiceInterface $gameService
     */
    public function __construct(protected GameServiceInterface $gameService)
    {
    }

    /**
     * @param Request $request
     * @param int $tournamentId
     * @return AnonymousResourceCollection
     */
    public function getByTournament(Request $request, int $tournamentId): AnonymousResourceCollection
    {
        return GameResources::collection(
            $this->gameService->getGamesByTournamentId(
                tournamentId: $tournamentId,
                relations: [
                    "homeTeam:id,name",
                    "awayTeam:id,name"
                ],
                groupBy: $request->input('group_by') ?? null,
            )
        );
    }

    /**
     * @param int $tournamentId
     * @return AnonymousResourceCollection
     */
    public function getTournamentCurrentWeek(int $tournamentId): AnonymousResourceCollection
    {
        return GameResources::collection(
            $this->gameService->getGamesByTournamentIdAndCurrentWeek(
                tournamentId: $tournamentId,
                relations: [
                    "homeTeam:id,name",
                    "awayTeam:id,name"
                ],
            )
        );
    }

    /**
     * @param int $tournamentId
     * @return SuccessResponse
     * @throws ValidationException
     */
    public function playGamesOfCurrentWeek(int $tournamentId): SuccessResponse
    {
        $this->gameService->playGamesOfCurrentWeek($tournamentId);
        return new SuccessResponse();
    }

    /**
     * @param int $tournamentId
     * @return SuccessResponse
     * @throws ValidationException
     */
    public function playAllGamesOfTournament(int $tournamentId): SuccessResponse
    {
        $this->gameService->playAllGamesOfTournament($tournamentId);
        return new SuccessResponse();
    }
}