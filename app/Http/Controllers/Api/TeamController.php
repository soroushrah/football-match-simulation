<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Team\TeamStoreDTO;
use App\DTOs\Team\TeamUpdateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamStoreRequest;
use App\Http\Requests\Team\TeamUpdateRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\TeamResources;
use App\Models\Team;
use App\Services\Contracts\TeamServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamController extends Controller
{
    public function __construct(protected TeamServiceInterface $teamService)
    {
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return TeamResources::collection($this->teamService->all(pagination: $request->input("pagination") ?? null));
    }

    /**
     * @param TeamStoreRequest $request
     * @return TeamResources
     */
    public function store(TeamStoreRequest $request): TeamResources
    {
        return new TeamResources(
            $this->teamService->store(
                new TeamStoreDTO(
                    name: $request->name,
                    power: $request->power,
                    tournamentId: $request->tournament_id ?? null
                )
            )
        );
    }

    /**
     * @param TeamUpdateRequest $request
     * @param int $id
     * @return TeamResources
     */
    public function update(TeamUpdateRequest $request, int $id): TeamResources
    {
        return new TeamResources(
            $this->teamService->update(
                new TeamUpdateDTO(
                    id: $id,
                    name: $request->name,
                    power: $request->power
                ),
            )
        );
    }

    /**
     * @param int $id
     * @return SuccessResponse
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->teamService->delete(id: $id, force: true);
        return new SuccessResponse();
    }
}