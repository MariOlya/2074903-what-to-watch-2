<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Factories\Dto\Dto;
use App\Models\Film;
use App\Models\FilmStatus;
use App\Models\Genre;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FilmRepository implements FilmRepositoryInterface
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PAGE_SIZE = 8;

    public function all(array $columns = ['*'], int $limit = self::DEFAULT_LIMIT, int $offset = self::DEFAULT_OFFSET): Collection
    {
        return Film::query()->limit($limit)->offset($offset)->get($columns);
    }

    public function update(int $id, Dto $dto): Model
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
        $newVotesAmount = ++$currentVotesAmount;
        $newRating = ($currentRating * $currentVotesAmount + $newVote) / $newVotesAmount;

        $film->update([
            'rating' => round($newRating, 2),
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

    public function paginateList(
        array $queryParams,
        array $columns = [
            'films.id as id',
            'name',
            'previewImage.link as preview_image',
            'previewVideoLink.link as preview_video_link'
        ]
    ): LengthAwarePaginator {
        $queryParams['limit'] = $queryParams['limit'] ?? self::DEFAULT_LIMIT;
        $queryParams['offset'] = $queryParams['offset'] ?? self::DEFAULT_OFFSET;
        $queryParams['pageSize'] = $queryParams['pageSize'] ?? self::DEFAULT_PAGE_SIZE;
        $queryParams['page'] = $queryParams['page'] ?? self::DEFAULT_PAGE;
        $queryParams['status'] = $queryParams['status'] ?? Film::FILM_DEFAULT_STATUS;
        $queryParams['order_by'] = $queryParams['order_by'] ?? Film::FILM_DEFAULT_ORDER_BY;
        $queryParams['order_to'] = $queryParams['order_to'] ?? Film::FILM_DEFAULT_ORDER_TO;

        $basedBuilder = Film::query()
            ->leftJoin('files as previewImage', 'films.preview_image_id', '=', 'previewImage.id')
            ->leftJoin('links as previewVideoLink', 'films.preview_video_link_id', '=', 'previewVideoLink.id')
            ->join('film_statuses', 'films.status_id', '=', 'film_statuses.id')
            ->where('film_statuses.status', '=', Film::FILM_DEFAULT_STATUS);

        if (isset($queryParams['genre'])) {
            $filmIds = array_map(
                static fn ($film) => $film['id'],
                Genre::whereGenre($queryParams['genre'])->first()?->films->toArray()
            );

            return $basedBuilder
                ->whereIn('films.id', $filmIds)
                ->orderBy($queryParams['order_by'], $queryParams['order_to'])
                ->limit($queryParams['limit'])
                ->offset($queryParams['offset'])
                ->paginate(
                    perPage: $queryParams['pageSize'],
                    columns: $columns,
                    page: $queryParams['page']
                );
        }

        return $basedBuilder
            ->orderBy($queryParams['order_by'], $queryParams['order_to'])
            ->limit($queryParams['limit'])
            ->offset($queryParams['offset'])
            ->paginate(
                perPage: $queryParams['pageSize'],
                columns: $columns,
                page: $queryParams['page']
            );
    }

    public function similarFilms(int $id, array $columns = [
        'films.id as id',
        'name',
        'previewImage.link as preview_image',
        'previewVideoLink.link as preview_video_link'
    ]
    ): ?\Illuminate\Support\Collection {
        $genreIds = array_map(
            static fn ($genre) => $genre['id'],
            Film::whereId($id)->first()?->genres->toArray());

        return DB::table('films')
            ->leftJoin('files as previewImage', 'films.preview_image_id', '=', 'previewImage.id')
            ->leftJoin('links as previewVideoLink', 'films.preview_video_link_id', '=', 'previewVideoLink.id')
            ->join('film_genre', 'films.id', '=', 'film_genre.film_id')
            ->join('film_statuses', 'films.status_id', '=', 'film_statuses.id')
            ->whereIn('film_genre.genre_id', $genreIds)
            ->whereNot('film_id', '=', $id)
            ->where('film_statuses.status', '=', Film::FILM_DEFAULT_STATUS)
            ->limit(4)
            ->get($columns);
    }
}
