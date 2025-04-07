<?php

namespace App\Models;

use App\DTOs\Tournament\TournamentStoreDTO;
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
 * @property int $total_weeks
 * @property int $current_week
 * @property bool $completed
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * Relations:
 * @property-read BelongsToMany| Collection<Team> $teams
 * @property-read HasMany|Collection<Game> $games
 */
class Tournament extends Model
{
    use SoftDeletes,HasFactory;

    protected $guarded = ["id"];


    //=============== QUERIES ================//
    public static function storeByDTO(TournamentStoreDTO $dto): Tournament
    {
        return Tournament::query()->create($dto->toArray());
    }


    //============== Relations ===============//

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, "team_tournament", "tournament_id", "team_id");
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class, "tournament_id", "id");
    }
}
