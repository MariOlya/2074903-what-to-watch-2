<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Factories\Dto\Dto;
use App\Factories\Interfaces\FilmFileFactoryInterface;
use App\Factories\Interfaces\LinkFactoryInterface;
use App\Models\Actor;
use App\Models\Color;
use App\Models\Director;
use App\Models\FileType;
use App\Models\Film;
use App\Models\FilmStatus;
use App\Models\Genre;
use App\Models\LinkType;
use App\Models\User;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as DbCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FilmRepository implements FilmRepositoryInterface
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PAGE_SIZE = 8;

    public function __construct(
        readonly FilmFileFactoryInterface $imageFactory,
        readonly LinkFactoryInterface $linkFactory,
    ) {
    }

    public function all(array $columns = ['*'], int $limit = self::DEFAULT_LIMIT, int $offset = self::DEFAULT_OFFSET): Collection
    {
        return Film::query()->limit($limit)->offset($offset)->get($columns);
    }

    /**
     * @param int $id
     * @param Dto $dto
     * @return Model
     * @throws \Exception
     */
    public function update(int $id, Dto $dto): Model
    {
        /** @var Film $film */
        $film = $this->findById($id);

        $newName = $dto->getParams()['name'] ?? null;
        $newPosterImage = $dto->getParams()['poster_image'] ?? null;
        $newPreviewImage = $dto->getParams()['preview_image'] ?? null;
        $newBackgroundImage = $dto->getParams()['background_image'] ?? null;
        $newBackgroundColor = $dto->getParams()['background_color'] ?? null;
        $newVideoLink = $dto->getParams()['video_link'] ?? null;
        $newPreviewVideoLink = $dto->getParams()['preview_video_link'] ?? null;
        $newDescription = $dto->getParams()['description'] ?? null;
        $newDirector = $dto->getParams()['director'] ?? null;
        $newActors = $dto->getParams()['starring'] ?? null;
        $newGenres = $dto->getParams()['genre'] ?? null;
        $newRunTime = $dto->getParams()['run_time'] ?? null;
        $newReleasedYear = $dto->getParams()['released'] ?? null;
        $newImdbId = $dto->getParams()['imdb_id'] ?? null;
        $newStatus = $dto->getParams()['status'] ?? null;

        $previousPosterImage = $film->posterImage->link ?? null;
        $previousPreviewImage = $film->previewImage->link ?? null;
        $previousBackgroundImage = $film->backgroundImage->link ?? null;

        DB::beginTransaction();

        try {
            if ($newName && $newName !== $film->name) {
                $film->name = $newName;
            }

            if ($newPosterImage && $newPosterImage !== $previousPosterImage) {
                if ($previousPosterImage) {
                    $film->posterImage()->delete();
                }
                $posterImageId = $this->imageFactory->createFromEditForm($newPosterImage, FileType::POSTER_TYPE);
                $film->poster_image_id = $posterImageId;
            }

            if ($newPreviewImage && $newPreviewImage !== $previousPreviewImage) {
                if ($previousPreviewImage) {
                    $film->previewImage()->delete();
                }
                $previewImageId = $this->imageFactory->createFromEditForm($newPreviewImage, FileType::PREVIEW_TYPE);
                $film->preview_image_id = $previewImageId;
            }

            if ($newBackgroundImage && $newBackgroundImage !== $previousBackgroundImage) {
                if ($previousBackgroundImage) {
                    $film->backgroundImage()->delete();
                }
                $backgroundImageId = $this->imageFactory->createFromEditForm(
                    $newBackgroundImage,
                    FileType::BACKGROUND_TYPE
                );
                $film->background_image_id = $backgroundImageId;
            }

            if ($newBackgroundColor && $newBackgroundColor !== $film->backgroundColor->color) {
                $newColor = Color::query()->firstOrCreate([
                    'color' => $newBackgroundColor
                ]);

                $film->background_color_id = $newColor->id;
            }

            if ($newVideoLink && $newVideoLink !== $film->videoLink->link) {
                if ($film->videoLink->link) {
                    $film->videoLink()->delete();
                }
                $videoLinkId = $this->linkFactory->createNewLink($newVideoLink, LinkType::VIDEO_TYPE);
                $film->video_link_id = $videoLinkId;
            }

            if ($newPreviewVideoLink && $newPreviewVideoLink !== $film->previewVideoLink->link) {
                if ($film->previewVideoLink->link) {
                    $film->previewVideoLink()->delete();
                }
                $previewVideoLinkId = $this->linkFactory->createNewLink($newPreviewVideoLink, LinkType::PREVIEW_TYPE);
                $film->preview_video_link_id = $previewVideoLinkId;
            }

            if ($newDescription && $newDescription !== $film->description) {
                $film->description = $newDescription;
            }

            if ($newDirector && $newDirector !== $film->director->name) {
                $newDirectorLink = Director::query()->firstOrCreate([
                    'name' => $newDirector
                ]);

                $film->director_id = $newDirectorLink->id;
            }

            if ($newActors) {
                $newActorIds = [];
                foreach ($newActors as $newActor) {
                    $actor = Actor::query()->firstOrCreate([
                        'name' => $newActor
                    ]);
                    $newActorIds[] = $actor->id;
                }

                $film->actors()->sync($newActorIds);
            }

            if ($newGenres) {
                $newGenreIds = [];
                foreach ($newGenres as $newGenre) {
                    $genre = Genre::query()->firstOrCreate([
                        'genre' => $newGenre
                    ]);
                    $newGenreIds[] = $genre->id;
                }

                $film->genres()->sync($newGenreIds);
            }

            if ($newRunTime && $newRunTime !== $film->run_time) {
                $film->run_time = $newRunTime;
            }

            if ($newReleasedYear && $newReleasedYear !== $film->released) {
                $film->released = $newReleasedYear;
            }

            if ($newImdbId && $newImdbId !== $film->imdb_id) {
                $film->imdb_id = $newImdbId;
            }

            if ($newStatus && $newStatus !== $film->status->status) {
                $film->status_id = FilmStatus::whereStatus($newStatus)->value('id');
            }

            $film->save();

            DB::commit();

            if ($previousPosterImage && $newPosterImage !== $previousPosterImage) {
                FileService::deleteFileFromStorage(substr($previousPosterImage, 4));
            }

            if ($previousPreviewImage && $newPreviewImage !== $previousPreviewImage) {
                FileService::deleteFileFromStorage(substr($previousPreviewImage, 4));
            }

            if ($previousBackgroundImage && $newBackgroundImage !== $previousBackgroundImage) {
                FileService::deleteFileFromStorage(substr($previousBackgroundImage, 4));
            }

            return $film;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateRating(int $id, int $newVote): Model
    {
        /** @var Film $film */
        $film = Film::whereId($id)->first();

        if (!$film) {
            throw new NotFoundHttpException('This film is not found', null, 404);
        }

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
        return Film::with([
            'posterImage',
            'previewImage',
            'backgroundImage',
            'backgroundColor',
            'videoLink',
            'previewVideoLink',
            'director',
            'actors',
            'genres'
        ])->find($id, $columns);
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
            ->where('film_statuses.status', '=', $queryParams['status']);

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
    ): ?DbCollection {
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

    public function favoriteFilms(int $userId, array $columns = [
        'films.id as id',
        'name',
        'previewImage.link as preview_image',
        'previewVideoLink.link as preview_video_link'
    ]
    ): LengthAwarePaginator {
        $filmIds = array_map(
            static fn ($film) => $film['id'],
            User::whereId($userId)->first()?->favoriteFilms->toArray());

        return Film::query()
            ->leftJoin('files as previewImage', 'films.preview_image_id', '=', 'previewImage.id')
            ->leftJoin('links as previewVideoLink', 'films.preview_video_link_id', '=', 'previewVideoLink.id')
            ->join('film_statuses', 'films.status_id', '=', 'film_statuses.id')
            ->whereIn('films.id', $filmIds)
            ->where('film_statuses.status', '=', Film::FILM_DEFAULT_STATUS)
            ->orderBy(Film::FILM_DEFAULT_ORDER_BY, Film::FILM_DEFAULT_ORDER_TO)
            ->limit(self::DEFAULT_LIMIT)
            ->offset(self::DEFAULT_OFFSET)
            ->paginate(
                perPage: self::DEFAULT_PAGE_SIZE,
                columns: $columns,
                page: self::DEFAULT_PAGE
            );
    }
}
