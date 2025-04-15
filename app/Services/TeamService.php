<?php

namespace App\Services;

use App\DTOs\Team\TeamStoreDTO;
use App\DTOs\Team\TeamUpdateDTO;
use App\Models\Team;
use App\Services\Contracts\TeamServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class TeamService implements TeamServiceInterface
{
    /** @inheritDoc */
    public function all(
        array $filters = [],
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator {
        $query = Team::query()->with($relations);
        return $pagination ? $query->paginate($pagination) : $query->get();
    }

    /** @inheritDoc */
    public function show(int $id, array $relations = []): Model
    {
        return Team::query()->with($relations)->findOrFail($id);
    }

    /** @inheritDoc */
    public function store(TeamStoreDTO $DTO): Team
    {
        $team = Team::storeByDTO($DTO);

        //Store In Tournament Simultaneously Team Created
        if ($DTO->getTournamentId()) {
            $this->registerTeamInTournament(teamId: $team->id, tournamentId: $DTO->getTournamentId());
        }

        return $team->fresh();
    }

    /** @inheritDoc */
    public function update(TeamUpdateDTO $DTO): Team
    {
        Team::updateByDTO($DTO);
        /** @var Team */
        return $this->show($DTO->getId());
    }

    /** @inheritDoc */
    public function registerTeamInTournament(int $teamId, int $tournamentId): Model
    {
        /** @var Team $team */
        $team = $this->show(id: $teamId);
        $team->tournaments()->sync(ids: [$tournamentId], detaching: false);
        return $team;
    }

    /** @inheritDoc
     * @throws ValidationException
     */
    public function delete(int $id, bool $force = false): bool
    {
        /** @var Team $team */
        $team = $this->show(id: $id, relations: ["tournaments"]);

        /*if ($team->tournaments->count() > 0) {
            throw ValidationException::withMessages([
                "team" => __("validation.exceptions.team.already_participated_in_tournament"),
            ]);
        }*/

        return $force ? $team->forceDelete() : $team->delete();
    }

    /** @inheritDoc */
    public function getAllTeamsInTournament(int $tournamentId): Collection
    {
        return Team::query()->whereHas('tournaments', function ($query) use ($tournamentId) {
            $query->where('id', $tournamentId);
        })->get();
    }
}