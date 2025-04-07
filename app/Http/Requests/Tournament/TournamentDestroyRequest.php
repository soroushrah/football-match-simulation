<?php

namespace App\Http\Requests\Tournament;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property bool $force
 */
class TournamentDestroyRequest extends BaseFormRequest
{

    function rules(): array
    {
        return [
            "force" => ["sometimes", "boolean"],
        ];
    }
}
