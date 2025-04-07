<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

/**
 * @property string $name
 * @property int $power
 */
class TeamUpdateRequest extends BaseFormRequest
{

    function rules(): array
    {
        $id = Route::current()->parameter('id');
        return [
            "name" => ["required", "string", "max:255", Rule::unique("teams", "name")->ignore($id)],
            "power" => ["required", "int","min:1" , "max:100" ],
        ];
    }
}