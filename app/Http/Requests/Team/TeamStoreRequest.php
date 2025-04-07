<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $name
 * @property int $power
 * @property int $tournament_id
 */
class TeamStoreRequest extends BaseFormRequest
{

    function rules(): array
    {
        return [
            "name" => ["required", "string", "max:255", "unique:teams,name"],
            "power" => ["required", "int", "min:1", "max:100"],
            "tournament_id" => ["sometimes", "integer", "exists:tournaments,id"],
        ];
    }
}