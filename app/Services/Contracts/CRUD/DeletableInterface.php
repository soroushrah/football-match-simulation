<?php

namespace App\Services\Contracts\CRUD;

use Illuminate\Database\Eloquent\Model;

interface DeletableInterface
{
    /**
     * @param int $id
     * @param bool $force
     * @return bool
     */
    public function delete(int $id,bool $force = false):bool;

}