<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface ReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function allForFilm(
        int $filmId,
        array $columns = ['*'],
        int $limit = self::DEFAULT_LIMIT,
        int $offset = self::DEFAULT_OFFSET
    ): Collection;

    public function deleteChildReviews(int $reviewId): void;
}
