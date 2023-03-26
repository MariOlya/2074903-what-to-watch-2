<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Factories\Dto\Dto;
use App\Models\Film;
use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FilmRepository implements RepositoryInterface
{
    public function all(array $columns = ['*']): Collection
    {
        return Film::all($columns);
    }

    public function update(Dto $data, int $id): Model
    {
        $film = Film::whereId($id);
        // TODO: Implement update() method.
        $film->update();
        return $film;
    }

    public function updateRating(int $id, int $newVote): Model
    {
        $film = $this->findById($id);

        if (!$film) {
            throw new NotFoundHttpException('This film is not found', null, 404);
        }

        /** @var Film $film */
        $currentRating = $film->rating;
        $currentVotesAmount = $film->vote_amount;
        $newVotesAmount = $currentVotesAmount++;
        $newRating = ($currentRating * $currentVotesAmount + $newVote) / $newVotesAmount;

        $film->update([
            'rating' => $newRating,
            'vote_amount' => $newVotesAmount
        ], ['touch' => false]);

        return $film;
    }

    public function delete(int $id): void
    {
        $film = Film::whereId($id);
        $film->delete();
    }

    public function findById(int $id, array $columns = ['*']): ?Model
    {
        return Film::query()->find($id, $columns);
    }

    public function findByImdbId(string $imdbId, array $columns = ['*']): ?Model
    {
        return $this->findBy('imdb_id', $imdbId, $columns);
    }

    public function findBy(string $field, mixed $value, array $columns = ['*']): ?Model
    {
        return Film::query()->where($field, '=', $value)->first($columns);
    }
}
