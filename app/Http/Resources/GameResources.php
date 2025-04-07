<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $games = $this->resource;
        if ($games instanceof Collection) {
            $response = [
                "week" => 1,
                "games" => []
            ];

            foreach ($games as $key => $game) {
                if ($key == 0) {
                    $response["week"] = $game->week;
                }
                $response["games"][] = $game;
            }

            return $response;
        }
        return  parent::toArray($request);
    }
}
