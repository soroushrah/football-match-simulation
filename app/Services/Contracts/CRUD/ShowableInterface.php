<?php

namespace App\Services\Contracts\CRUD;

use Illuminate\Database\Eloquent\Model;

interface ShowableInterface
{
    /**
     * @param int $id
     * @param array $relations
     * @return Model
     */
    public function show(int $id, array $relations = []): Model;

}