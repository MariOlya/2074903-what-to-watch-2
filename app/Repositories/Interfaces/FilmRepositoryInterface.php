<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Factories\Dto\Dto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface FilmRepositoryInterface extends BaseRepositoryInterface
{
    public function updateRating(int $id, int $newVote, int $latestVote = null): Model;

    public function findByImdbId(string $imdbId, array $columns = ['*']): Model;

    public function paginateList(array $queryParams, array $columns = ['*']): LengthAwarePaginator;

    public function similarFilms(int $id, array $columns = ['*']): ?Collection;

    public function favoriteFilms(int $userId, array $columns = ['*']): LengthAwarePaginator;

    public function fillFilmInfo(string $imdbId, Dto $dto): Model;

    public function fillAdditionalFilmInfo(string $imdbId, Dto $dto): Model;

    public function findPromo(array $columns = ['*']): ?Model;
}
