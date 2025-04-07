<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $tournament_id
 * @property int $team_id
 * @property int $points
 * @property int $goals_for
 * @property int $goals_against
 * @property int $goals_different
 * @property int $game_win
 * @property int $game_draw
 * @property int $game_lose
 * @property int $games_played
 * @property int $champion_prediction
 * @property int $rank
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations :
 * @property-read BelongsTo | Tournament $tournament
 * @property-read BelongsTo | Team $team
 */
class Scoreboard extends Model
{
    protected $guarded = ["id"];


    //======== Relations ===========//

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
