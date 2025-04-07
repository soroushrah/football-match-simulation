<?php

namespace App\DTOs\Team;

use App\DTOs\BaseDTO;

class TeamUpdateDTO extends BaseDTO
{
    public function __construct(
        protected int $id,
        protected string $name,
          protected int $power
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPower(): int
    {
        if ($this->power < 0) {
            return 1;
        }elseif ($this->power > 100) {
            return 100;
        }else{
            return $this->power;
        }
    }

    public function toArray(): array
    {
        return [
            "name" => $this->getName(),
            "power" => $this->getPower(),
        ];
    }
}