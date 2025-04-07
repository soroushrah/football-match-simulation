<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Tournament\TournamentStoreDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tournament\TournamentDestroyRequest;
use App\Http\Requests\Tournament\TournamentStoreRequest;
use App\Http\Resources\GameResources;
use App\Http\Resources\ScoreboardResources;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\TournamentResources;
use App\Services\Contracts\GameServiceInterface;
use App\Services\Contracts\ScoreboardServiceInterface;
use App\Services\Contracts\TournamentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class ScoreboardController extends Controller
{
    /**
     * @param ScoreboardServiceInterface $scoreboardService
     */
    public function __construct(protected ScoreboardServiceInterface $scoreboardService)
    {
    }

    /**
     * @param int $tournamentId
     * @return AnonymousResourceCollection
     */
    public function getByTournament(int $tournamentId): AnonymousResourceCollection
    {
        return ScoreboardResources::collection(
            $this->scoreboardService->getScoreboardsByTournament(
                tournamentId: $tournamentId,
                relations: ["team"]
            )
        );
    }
}