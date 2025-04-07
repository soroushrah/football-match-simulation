<?php

namespace App\Http\Resources;

use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Tournament $tournament */
        $tournament = $this->resource;
        return [
            "id" => $tournament->id,
            "name" => $tournament->name,
            "total_weeks" => $tournament->total_weeks,
            "current_week" => $tournament->current_week,
            "completed" => (bool)$tournament->completed,
            "created_at" => $tournament->created_at?->format('Y-m-d H:i:s'),
            "updated_at" => $tournament->updated_at?->format('Y-m-d H:i:s'),
            "teams" => TeamResources::collection($tournament->teams),
        ];
    }
}
