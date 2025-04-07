<?php

namespace App\Models;

use App\DTOs\Team\TeamStoreDTO;
use App\DTOs\Team\TeamUpdateDTO;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property int $power
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * Relations:
 * @property-read BelongsToMany | Collection<Tournament> $tournaments
 * @property-read BelongsToMany | Collection<Game> $homeGames
 * @property-read BelongsToMany | Collection<Game> $awayGames
 */
class Team extends Model
{
    use SoftDeletes,HasFactory;

    protected $guarded = ["id"];

    //=============== QUERIES ================//
    public static function storeByDTO(TeamStoreDTO $dto): Team
    {
        return Team::query()->create($dto->toArray());
    }

    public static function updateByDTO(TeamUpdateDTO $dto): int
    {
        return Team::query()->where("id", $dto->getId())->update($dto->toArray());
    }

    //============== Relations ===============//
    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class, "team_tournament", "team_id","tournament_id" );
    }

    public function homeGames(): HasMany
    {
        return $this->hasMany(Game::class,"home_team_id");
    }

    public function awayGames(): HasMany
    {
        return $this->hasMany(Game::class,"away_team_id");
    }
}
