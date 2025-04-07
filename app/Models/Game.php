<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $week
 * @property int $tournament_id
 * @property int $home_team_id
 * @property int $away_team_id
 * @property int $home_goals
 * @property int $away_goals
 * @property bool $played
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations:
 * @property-read BelongsTo | Tournament $tournament
 * @property-read BelongsTo | Team $homeTeam
 * @property-read BelongsTo | Team $awayTeam
 */
class Game extends Model
{
    protected $guarded = ["id"];


    //============== Relations ===============//
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }


    /**
     * @return BelongsTo
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, "home_team_id");
    }

    /**
     * @return BelongsTo
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, "away_team_id");
    }


}
