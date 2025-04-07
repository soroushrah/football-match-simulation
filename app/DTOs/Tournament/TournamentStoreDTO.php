<?php

namespace App\DTOs\Tournament;

use App\DTOs\BaseDTO;

class TournamentStoreDTO extends BaseDTO
{
    public function __construct(protected string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            "name" => $this->getName()
        ];
    }
}