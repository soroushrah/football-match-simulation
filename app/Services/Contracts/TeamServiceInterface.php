<?php

namespace App\Services\Contracts;

use App\DTOs\Team\TeamStoreDTO;
use App\DTOs\Team\TeamUpdateDTO;
use App\Models\Team;
use App\Services\Contracts\CRUD\DeletableInterface;
use App\Services\Contracts\CRUD\ListableInterface;
use App\Services\Contracts\CRUD\ShowableInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TeamServiceInterface extends ListableInterface, ShowableInterface,DeletableInterface
{

    /**
     * @param TeamStoreDTO $DTO
     * @return Model
     */
    public function store(TeamStoreDTO $DTO): Team;

    /**
     * @param TeamUpdateDTO $DTO
     * @return Model
     */
    public function update(TeamUpdateDTO $DTO): Team;

    /**
     * @param int $teamId
     * @param int $tournamentId
     * @return Model
     */
    public function registerTeamInTournament(int $teamId, int $tournamentId): Model;


    /**
     * @param int $tournamentId
     * @return Collection
     */
    public function getAllTeamsInTournament(int $tournamentId): Collection;
}