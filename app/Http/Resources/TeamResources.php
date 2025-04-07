<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Team $team */
        $team = $this->resource;
        return [
            'id' => $team->id,
            'name' => $team->name,
            "power" => $team->power,
            "created_at" => $team->created_at?->format('Y-m-d H:i:s'),
            "updated_at" => $team->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}