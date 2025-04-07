<?php

namespace App\DTOs\Team;

use App\DTOs\BaseDTO;

class TeamStoreDTO extends BaseDTO
{
    public function __construct(
        protected string $name,
        protected int $power,
        protected ?int $tournamentId = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPower(): int
    {
        if ($this->power < 0) {
            return 1;
        } elseif ($this->power > 100) {
            return 100;
        } else {
            return $this->power;
        }
    }

    public function getTournamentId(): ?int
    {
        return $this->tournamentId;
    }

    public function toArray(): array
    {
        return [
            "name" => $this->getName(),
            "power" => $this->getPower()
        ];
    }
}