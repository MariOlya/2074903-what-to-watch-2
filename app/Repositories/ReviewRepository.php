<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Factories\Dto\Dto;
use App\Models\Review;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function all(
        array $columns = ['*'],
        int $limit = self::DEFAULT_LIMIT,
        int $offset = self::DEFAULT_OFFSET
    ): Collection {
        return Review::query()->limit($limit)->offset($offset)->get($columns);

    }

    public function update(int $id, Dto $dto): Model
    {
        // TODO: Implement update() method.
    }

    public function delete(int $id): void
    {
        // TODO: Implement delete() method.
    }

    public function findById(int $id, array $columns = ['*']): ?Model
    {
        return Review::with([
            'comments',
        ])->find($id, $columns);
    }

    public function findBy(string $field, mixed $value, array $columns = ['*']): ?Model
    {
        return Review::with([
            'comments',
        ])->where($field, '=', $value)->first($columns);
    }

    public function allForFilm(
        int $filmId,
        array $columns = ['id', 'text', 'created_at', 'rating', 'user_id'],
        int $limit = self::DEFAULT_LIMIT,
        int $offset = self::DEFAULT_OFFSET
    ): Collection {
        return Review::with([
            'user:id,name'
        ])
            ->select($columns)
            ->where('film_id', '=', $filmId)
            ->orderBy(Review::REVIEW_DEFAULT_ORDER_BY, Review::REVIEW_DEFAULT_ORDER_TO)
            ->limit($limit)
            ->offset($offset)
            ->get();
    }
}
