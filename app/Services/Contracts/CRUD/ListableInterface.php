<?php

namespace App\Services\Contracts\CRUD;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ListableInterface
{
    /**
     * @param array $filters
     * @param array $relations
     * @param int|null $pagination
     * @return Collection|LengthAwarePaginator
     */
    public function all(
        array $filters = [],
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator;

}