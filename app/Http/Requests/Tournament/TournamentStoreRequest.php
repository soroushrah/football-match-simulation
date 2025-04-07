<?php

namespace App\Http\Requests\Tournament;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 */
class TournamentStoreRequest extends BaseFormRequest
{

    function rules(): array
    {
        return [
            "name" => ["required", "string", "max:255"],
        ];
    }
}
