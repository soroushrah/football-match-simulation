<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Tournament\TournamentStoreDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tournament\TournamentDestroyRequest;
use App\Http\Requests\Tournament\TournamentStoreRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\TournamentResources;
use App\Services\Contracts\TournamentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class TournamentController extends Controller
{

    /**
     * @param TournamentServiceInterface $tournamentService
     */
    public function __construct(protected TournamentServiceInterface $tournamentService)
    {
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return TournamentResources::collection(
            $this->tournamentService->all(
                relations: ["teams"],
                pagination: $request->input("pagination")
            )
        );
    }

    /**
     * @param int $id
     * @return TournamentResources
     */
    public function show(int $id): TournamentResources
    {
        return new TournamentResources($this->tournamentService->show($id));
    }

    /**
     * @param TournamentStoreRequest $request
     * @return TournamentResources
     */
    public function store(TournamentStoreRequest $request): TournamentResources
    {
        return new TournamentResources(
            $this->tournamentService->store(
                new TournamentStoreDTO(
                    name: $request->name,
                )
            )
        );
    }

    /**
     * @param TournamentDestroyRequest $request
     * @param int $id
     * @return SuccessResponse
     */
    public function destroy(TournamentDestroyRequest $request, int $id): SuccessResponse
    {
        $this->tournamentService->delete(
            id: $id,
            force: $request->force ?? false
        );
        return new SuccessResponse();
    }

    /**
     * @throws ValidationException
     */
    public function generateTournamentGames(int $tournamentId): SuccessResponse
    {
        $this->tournamentService->generateTournamentGames(tournamentId: $tournamentId);
        return new SuccessResponse();
    }


    /**
     * @throws ValidationException
     */
    public function reGenerateTournamentGames(int $tournamentId): SuccessResponse
    {
        $this->tournamentService->generateTournamentGames($tournamentId, regenerate: true);
        return new SuccessResponse();
    }
}