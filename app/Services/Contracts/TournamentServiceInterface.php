<?php

namespace App\Services\Contracts;

use App\DTOs\Tournament\TournamentStoreDTO;
use App\Models\Tournament;
use App\Services\Contracts\CRUD\DeletableInterface;
use App\Services\Contracts\CRUD\ListableInterface;
use App\Services\Contracts\CRUD\ShowableInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

interface TournamentServiceInterface extends ListableInterface, ShowableInterface, DeletableInterface
{
    /**
     * @param TournamentStoreDTO $DTO
     * @return Model
     */
    public function store(TournamentStoreDTO $DTO): Model;

    /**
     * @param int $tournamentId
     * @param bool $regenerate
     * @return void
     * @throws ValidationException
     */
    public function generateTournamentGames(int $tournamentId, bool $regenerate = false): void;

    /**
     * @param string $name
     * @param int $teamCount
     * @return Tournament
     * @throws Exception
     * @throws ValidationException
     */
    public function storeTournamentWithRandomTeams(string $name, int $teamCount = 4): Tournament;
}