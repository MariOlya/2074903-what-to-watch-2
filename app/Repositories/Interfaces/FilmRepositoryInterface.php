<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface FilmRepositoryInterface extends BaseRepositoryInterface
{
    public function updateRating(int $id, int $newVote): Model;

    public function findByImdbId(string $imdbId, array $columns = ['*']): ?Model;
}
