<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Factories\Dto\Dto;
use App\Factories\Dto\FilmDto;
use App\Factories\Dto\HtmlAcademyFilmApiDto;
use App\Factories\Dto\OmdbFilmApiDto;
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
        /** @var FilmDto $filmDto */
        $filmDto = $dto;

        $previousPosterImage = $film->posterImage->link ?? null;
        $previousPreviewImage = $film->previewImage->link ?? null;
        $previousBackgroundImage = $film->backgroundImage->link ?? null;

        DB::beginTransaction();

        try {
            if ($filmDto->name && $filmDto->name !== $film->name) {
                $film->name = $filmDto->name;
            }

            if ($filmDto->posterImage && $filmDto->posterImage !== $previousPosterImage) {
                if ($previousPosterImage) {
                    $film->posterImage()->delete();
                }
                $posterImageId = $this->imageFactory->createFromEditForm(
                    $filmDto->posterImage,
                    FileType::POSTER_TYPE
                );
                $film->poster_image_id = $posterImageId;
            }

            if ($filmDto->previewImage && $filmDto->previewImage !== $previousPreviewImage) {
                if ($previousPreviewImage) {
                    $film->previewImage()->delete();
                }
                $previewImageId = $this->imageFactory->createFromEditForm(
                    $filmDto->previewImage,
                    FileType::PREVIEW_TYPE
                );
                $film->preview_image_id = $previewImageId;
            }

            if ($filmDto->backgroundImage && $filmDto->backgroundImage !== $previousBackgroundImage) {
                if ($previousBackgroundImage) {
                    $film->backgroundImage()->delete();
                }
                $backgroundImageId = $this->imageFactory->createFromEditForm(
                    $filmDto->backgroundImage,
                    FileType::BACKGROUND_TYPE
                );
                $film->background_image_id = $backgroundImageId;
            }

            if ($filmDto->backgroundColor && $filmDto->backgroundColor !== $film->backgroundColor->color) {
                $newColor = Color::query()->firstOrCreate([
                    'color' => $filmDto->backgroundColor
                ]);

                $film->background_color_id = $newColor->id;
            }

            if ($filmDto->videoLink && $filmDto->videoLink !== $film->videoLink->link) {
                if ($film->videoLink->link) {
                    $film->videoLink()->delete();
                }
                $videoLinkId = $this->linkFactory->createNewLink($filmDto->videoLink, LinkType::VIDEO_TYPE);
                $film->video_link_id = $videoLinkId;
            }

            if ($filmDto->previewVideoLink && $filmDto->previewVideoLink !== $film->previewVideoLink->link) {
                if ($film->previewVideoLink->link) {
                    $film->previewVideoLink()->delete();
                }
                $previewVideoLinkId = $this->linkFactory->createNewLink(
                    $filmDto->previewVideoLink,
                    LinkType::PREVIEW_TYPE
                );
                $film->preview_video_link_id = $previewVideoLinkId;
            }

            if ($filmDto->description && $filmDto->description !== $film->description) {
                $film->description = $filmDto->description;
            }

            if ($filmDto->director && $filmDto->director !== $film->director->name) {
                $newDirectorLink = Director::query()->firstOrCreate([
                    'name' => $filmDto->director
                ]);

                $film->director_id = $newDirectorLink->id;
            }

            if ($filmDto->actors) {
                $alreadyExistedActors = Actor::query()->whereIn('name', $filmDto->actors)->get();

                $newActorIds = array_map(
                    static fn ($actor) => $actor['id'],
                    $alreadyExistedActors->toArray()
                );

                foreach ($filmDto->actors as $actor) {
                    $isExisted = $alreadyExistedActors->contains('name', '=', $actor);
                    if (!$isExisted) {
                        $newActor = Actor::query()->create([
                            'name' => $actor,
                        ]);
                        $newActorIds[] = $newActor->id;
                    }
                }

                $film->actors()->sync($newActorIds);
            }

            if ($filmDto->genres) {
                $alreadyExistedGenres = Genre::query()->whereIn('genre', $filmDto->genres)->get();

                $newGenreIds = array_map(
                    static fn ($genre) => $genre['id'],
                    $alreadyExistedGenres->toArray()
                );

                foreach ($filmDto->genres as $genre) {
                    $isExisted = $alreadyExistedGenres->contains('genre', '=', $genre);

                    if (!$isExisted) {
                        $newGenre = Genre::query()->create([
                            'genre' => $genre,
                        ]);
                        $newGenreIds[] = $newGenre->id;
                    }
                }

                $film->genres()->sync($newGenreIds);
            }

            if ($filmDto->runTime && $filmDto->runTime !== $film->run_time) {
                $film->run_time = $filmDto->runTime;
            }

            if ($filmDto->released && $filmDto->released !== $film->released) {
                $film->released = $filmDto->released;
            }

            if ($filmDto->imdbId && $filmDto->imdbId !== $film->imdb_id) {
                $film->imdb_id = $filmDto->imdbId;
            }

            if ($filmDto->status && $filmDto->status !== $film->status->status) {
                $film->status_id = FilmStatus::whereStatus($filmDto->status)->value('id');
            }

            $film->save();

            DB::commit();

            if ($previousPosterImage && $filmDto->posterImage !== $previousPosterImage) {
                FileService::deleteFileFromStorage(substr($previousPosterImage, 4));
            }

            if ($previousPreviewImage && $filmDto->previewImage !== $previousPreviewImage) {
                FileService::deleteFileFromStorage(substr($previousPreviewImage, 4));
            }

            if ($previousBackgroundImage && $filmDto->backgroundImage !== $previousBackgroundImage) {
                FileService::deleteFileFromStorage(substr($previousBackgroundImage, 4));
            }

            return $film;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateRating(int $id, int $newVote, int $latestVote = null): Model
    {
        /** @var Film $film */
        $film = Film::whereId($id)->first();

        if (!$film) {
            throw new NotFoundHttpException('This film is not found', null, 404);
        }

        $currentRating = $film->rating;
        $currentVotesAmount = $film->vote_amount;

        if ($latestVote) {
            $newVotesAmount = --$currentVotesAmount;
            $currentRating = ($currentRating * $currentVotesAmount - $latestVote) / $newVotesAmount;
            $currentVotesAmount = $newVotesAmount;
        }

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
        $film = Film::whereId($id)->firstOrFail();
        $film->delete();
    }

    public function findById(int $id, array $columns = ['*']): Model
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
        ])->where('id', '=', $id)->firstOrFail($columns);
    }

    public function findByImdbId(string $imdbId, array $columns = ['*']): Model
    {
        return $this->findBy('imdb_id', $imdbId, $columns);
    }

    public function findBy(string $field, mixed $value, array $columns = ['*']): Model
    {
        return Film::query()->where($field, '=', $value)->firstOrFail($columns);
    }

    public function paginateList(
        array $queryParams,
        array $columns = ['*']
    ): LengthAwarePaginator {
        $queryParams['limit'] = $queryParams['limit'] ?? self::DEFAULT_LIMIT;
        $queryParams['offset'] = $queryParams['offset'] ?? self::DEFAULT_OFFSET;
        $queryParams['pageSize'] = $queryParams['pageSize'] ?? self::DEFAULT_PAGE_SIZE;
        $queryParams['page'] = $queryParams['page'] ?? self::DEFAULT_PAGE;
        $queryParams['status'] = $queryParams['status'] ?? Film::FILM_DEFAULT_STATUS;
        $queryParams['order_by'] = $queryParams['order_by'] ?? Film::FILM_DEFAULT_ORDER_BY;
        $queryParams['order_to'] = $queryParams['order_to'] ?? Film::FILM_DEFAULT_ORDER_TO;
        $queryParams['genre'] = $queryParams['genre'] ?? null;

        return Film::query()
            ->whereHas('status', static function($query) use ($queryParams) {
                $query->where('status', '=', $queryParams['status']);
            })
            ->whereHas('genres', static function($query) use ($queryParams) {
                if ($queryParams['genre']) {
                    $query->where('genre', '=', $queryParams['genre']);
                }
            })
            ->with([
            'previewImage:id,link',
            'previewVideoLink:id,link',
            ])
            ->select(['id', 'name', 'preview_image_id', 'preview_video_link_id', 'released', 'rating'])
            ->orderBy($queryParams['order_by'], $queryParams['order_to'])
            ->limit($queryParams['limit'])
            ->offset($queryParams['offset'])
            ->paginate(
                perPage: $queryParams['pageSize'],
                page: $queryParams['page']
            );
    }

    public function similarFilms(int $id, array $columns = ['*']): ?Collection
    {
        $genres = array_map(
            static fn ($genre) => $genre['genre'],
            Film::whereId($id)->firstOrFail()->genres->toArray()
        );

        return Film::query()
            ->whereHas('status', static function($query) {
                $query->where('status', '=', Film::FILM_DEFAULT_STATUS);
            })
            ->whereHas('genres', static function($query) use ($genres) {
                if ($genres) {
                    $query->whereIn('genre', $genres);
                }
            })
            ->with([
                'previewImage:id,link',
                'previewVideoLink:id,link',
            ])
            ->select(['id', 'name', 'preview_image_id', 'preview_video_link_id'])
            ->whereNot('id', '=', $id)
            ->limit(4)
            ->get();
    }

    public function favoriteFilms(int $userId, array $columns = ['*']): LengthAwarePaginator
    {
        $filmIds = array_map(
            static fn ($film) => $film['id'],
            User::whereId($userId)->firstOrFail()->favoriteFilms->toArray()
        );

        return Film::query()
            ->whereHas('status', static function($query) {
                $query->where('status', '=', Film::FILM_DEFAULT_STATUS);
            })
            ->with([
                'previewImage:id,link',
                'previewVideoLink:id,link',
            ])
            ->select(['id', 'name', 'preview_image_id', 'preview_video_link_id'])
            ->whereIn('films.id', $filmIds)
            ->orderBy(Film::FILM_DEFAULT_ORDER_BY, Film::FILM_DEFAULT_ORDER_TO)
            ->limit(self::DEFAULT_LIMIT)
            ->offset(self::DEFAULT_OFFSET)
            ->paginate(
                perPage: self::DEFAULT_PAGE_SIZE,
                page: self::DEFAULT_PAGE
            );
    }

    /**
     * @param string $imdbId
     * @param Dto $dto
     * @return Model
     * @throws \Exception
     */
    public function fillFilmInfo(string $imdbId, Dto $dto): Model
    {
        /** @var Film $updatedFilm */
        $updatedFilm = $this->findByImdbId($imdbId);
        /** @var OmdbFilmApiDto $omdbFilmApiDto */
        $omdbFilmApiDto = $dto;

        DB::beginTransaction();

        try {
            if ($omdbFilmApiDto->posterImage) {
                $posterImage = $this->imageFactory->createFromExternalApi(
                    $omdbFilmApiDto->posterImage,
                    FileType::POSTER_TYPE,
                    $omdbFilmApiDto->title
                );

                $previewImage = $this->imageFactory->createFromExternalApi(
                    $omdbFilmApiDto->posterImage,
                    FileType::PREVIEW_TYPE,
                    $omdbFilmApiDto->title
                );

                $updatedFilm->poster_image_id = $posterImage->id;
                $updatedFilm->preview_image_id = $previewImage->id;
            }

            if ($omdbFilmApiDto->released) {
                $releasedYear = substr($omdbFilmApiDto->released, -4, 4);
                $updatedFilm->released = (int)$releasedYear;
            }

            if ($omdbFilmApiDto->director) {
                $directorId = Director::query()->firstOrCreate([
                    'name' => $omdbFilmApiDto->director
                ])->id;
                $updatedFilm->director_id = $directorId;
            }

            if ($omdbFilmApiDto->runTime) {
                $runtime = substr($omdbFilmApiDto->runTime, 0, -4);
                $updatedFilm->run_time = (int)$runtime;
            }

            if ($omdbFilmApiDto->genres) {
                $genres = explode(', ', $omdbFilmApiDto->genres);
                $newGenres = array_map(
                    static fn ($genre) => strtolower($genre),
                    $genres
                );

                $alreadyExistedGenres = Genre::query()->whereIn('genre', $newGenres)->get();

                $newGenreIds = array_map(
                    static fn ($genre) => $genre['id'],
                    $alreadyExistedGenres->toArray()
                );

                foreach ($newGenres as $genre) {
                    $isExisted = $alreadyExistedGenres->contains('genre', '=', $genre);
                    if (!$isExisted) {
                        $newGenre = Genre::query()->create([
                            'genre' => $genre,
                        ]);
                        $newGenreIds[] = $newGenre->id;
                    }
                }

                $updatedFilm->genres()->sync($newGenreIds);
            }

            if ($omdbFilmApiDto->actors) {
                $actors = explode(', ', $omdbFilmApiDto->actors);

                $alreadyExistedActors = Actor::query()->whereIn('name', $actors)->get();

                $newActorIds = array_map(
                    static fn ($actor) => $actor['id'],
                    $alreadyExistedActors->toArray()
                );

                foreach ($actors as $actor) {
                    $isExisted = $alreadyExistedActors->contains('name', '=', $actor);
                    if (!$isExisted) {
                        $newActor = Actor::query()->create([
                            'name' => $actor,
                        ]);
                        $newActorIds[] = $newActor->id;
                    }
                }

                $updatedFilm->actors()->sync($newActorIds);
            }

            if ($omdbFilmApiDto->title) {
                $updatedFilm->name = $omdbFilmApiDto->title;
            }

            if ($omdbFilmApiDto->description) {
                $updatedFilm->description = $omdbFilmApiDto->description;
            }

            if ($omdbFilmApiDto->rating) {
                $updatedFilm->rating = (float)$omdbFilmApiDto->rating;
            }

            if ($omdbFilmApiDto->amountVotes) {
                $updatedFilm->vote_amount = (int)str_replace(',', '', $omdbFilmApiDto->amountVotes);
            }

            $updatedFilm->status_id = FilmStatus::whereStatus(Film::MODERATE_FILM_STATUS)->value('id');

            $updatedFilm->save();

            DB::commit();

            if ($omdbFilmApiDto->posterImage){
                FileService::addFileToStorage(
                    $omdbFilmApiDto->posterImage,
                    substr($posterImage->link, 4)
                );

                FileService::addFileToStorage(
                    $omdbFilmApiDto->posterImage,
                    substr($previewImage->link, 4)
                );
            }

            return $updatedFilm;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @param string $imdbId
     * @param Dto $dto
     * @return Model
     * @throws \Exception
     */
    public function fillAdditionalFilmInfo(string $imdbId, Dto $dto): Model
    {
        /** @var Film $updatedFilm */
        $updatedFilm = $this->findByImdbId($imdbId);
        /** @var HtmlAcademyFilmApiDto $htmlAcademyFilmApiDto */
        $htmlAcademyFilmApiDto = $dto;

        $previousPreviewImage = $updatedFilm->previewImage->link ?? null;

        DB::beginTransaction();

        try {
            if ($htmlAcademyFilmApiDto->previewImage) {
                $updatedFilm->previewImage()->delete();

                $newPreviewImage = $this->imageFactory->createFromExternalApi(
                    $htmlAcademyFilmApiDto->previewImage,
                    FileType::PREVIEW_TYPE,
                    $htmlAcademyFilmApiDto->title
                );

                $updatedFilm->preview_image_id = $newPreviewImage->id;
            }

            if ($htmlAcademyFilmApiDto->backgroundImage) {
                $backgroundImage = $this->imageFactory->createFromExternalApi(
                    $htmlAcademyFilmApiDto->backgroundImage,
                    FileType::BACKGROUND_TYPE,
                    $htmlAcademyFilmApiDto->title
                );

                $updatedFilm->background_image_id = $backgroundImage->id;
            }

            if ($htmlAcademyFilmApiDto->videoLink) {
                $videoLinkId = $this->linkFactory->createNewLink($htmlAcademyFilmApiDto->videoLink, LinkType::VIDEO_TYPE);
                $updatedFilm->video_link_id = $videoLinkId;
            }

            if ($htmlAcademyFilmApiDto->previewVideoLink) {
                $previewVideoLinkId = $this->linkFactory->createNewLink($htmlAcademyFilmApiDto->previewVideoLink, LinkType::PREVIEW_TYPE);
                $updatedFilm->preview_video_link_id = $previewVideoLinkId;
            }

            $updatedFilm->save();

            DB::commit();

            if ($htmlAcademyFilmApiDto->previewImage){
                FileService::addFileToStorage(
                    $htmlAcademyFilmApiDto->previewImage,
                    substr($newPreviewImage->link, 4)
                );
                if ($previousPreviewImage) {
                    FileService::deleteFileFromStorage(substr($previousPreviewImage, 4));
                }
            }

            if ($htmlAcademyFilmApiDto->backgroundImage){
                FileService::addFileToStorage(
                    $htmlAcademyFilmApiDto->backgroundImage,
                    substr($backgroundImage->link, 4)
                );
            }

            return $updatedFilm;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function findPromo(array $columns = ['*']): Model
    {
        return Film::wherePromo('true')->firstOrFail($columns);
    }
}
