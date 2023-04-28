<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Factories\Dto\FilmDto;
use App\Factories\Interfaces\FilmFactoryInterface;
use App\Http\Requests\NewFilmRequest;
use App\Http\Requests\UpdatingFilmRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnprocessableResponse;
use App\Jobs\ParseFilmInfoJob;
use App\Models\User;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FilmController extends Controller
{
    public function __construct(
        readonly FilmRepositoryInterface $filmRepository,
        readonly FilmFactoryInterface $filmFactory,
        readonly ReviewRepositoryInterface $reviewRepository,
    )
    {
    }

    /**
     * POLICY: For user we need to add isFavorite
     *
     * @param Request $request
     * @param int $filmId
     * @return BaseResponse
     */
    public function getFilmInfo(Request $request, int $filmId): BaseResponse
    {
        $film = $this->filmRepository->findById($filmId);

        /** @var User $currentUser */
        $currentUser = $request->user('sanctum');

        if ($currentUser) {
            $isFavorite = (bool)$currentUser->favoriteFilms()->where('film_id', '=', $filmId)->first();
            $film = $film->toArray();
            $film['is_favorite'] = $isFavorite;
        }

        return new SuccessResponse(data: $film);
    }

    /**
     * POLICY: Only for user
     *
     * @param int $filmId
     * @return BaseResponse
     */
    public function addFavoriteFilm(int $filmId): BaseResponse
    {
        $film = $this->filmRepository->findById($filmId);

        /** @var User $user */
        $user = Auth::user();

        if ($user->favoriteFilms()->where('film_id', '=', $filmId)->first() !== null) {
            return new UnprocessableResponse();
        }

        $user->favoriteFilms()->attach($filmId);

        return new SuccessResponse(
            data: $film
        );
    }

    /**
     * POLICY: Only for user
     *
     * @param int $filmId
     * @return BaseResponse
     */
    public function deleteFavoriteFilm(int $filmId): BaseResponse
    {
        $film = $this->filmRepository->findById($filmId);

        /** @var User $user */
        $user = Auth::user();

        if ($user->favoriteFilms()->where('film_id', '=', $filmId)->first() === null) {
            return new UnprocessableResponse();
        }

        $user->favoriteFilms()->detach($filmId);

        return new SuccessResponse(
            data: ['This film was deleted from your favorite list']
        );
    }

    /**
     * POLICY: Only for moderator
     *
     * @param NewFilmRequest $request
     * @return BaseResponse
     */
    public function addNewFilm(NewFilmRequest $request): BaseResponse
    {
        $imdbId = $request->validated()['imdb_id'];

        $newFilm = $this->filmFactory->createNewFilm($imdbId);

        ParseFilmInfoJob::dispatch($imdbId);

        return new SuccessResponse(
            codeResponse: Response::HTTP_CREATED,
            data: $newFilm
        );
    }

    /**
     * POLICY: Only for moderator
     *
     * @param UpdatingFilmRequest $request
     * @param int $filmId
     * @return BaseResponse
     */
    public function updateFilm(UpdatingFilmRequest $request, int $filmId): BaseResponse
    {
        $params = $request->validated();

        $filmDto = new FilmDto(
            name: $params['name'] ?? null,
            posterImage: $params['poster_image'] ?? null,
            previewImage: $params['preview_image'] ?? null,
            backgroundImage: $params['background_image'] ?? null,
            backgroundColor: $params['background_color'] ?? null,
            videoLink: $params['video_link'] ?? null,
            previewVideoLink: $params['preview_video_link'] ?? null,
            description: $params['description'] ?? null,
            director: $params['director'] ?? null,
            actors: $params['starring'] ?? null,
            genres: $params['genre'] ?? null,
            runTime: $params['run_time'] ?? null,
            released: $params['released'] ?? null,
            imdbId: $params['imdb_id'] ?? null,
            status: $params['status'] ?? null
        );

        $updatedFilm = $this->filmRepository->update($filmId, $filmDto);

        return new SuccessResponse(
            data: $updatedFilm
        );
    }

    /**
     * POLICY: Available for all
     *
     * @param int $filmId
     * @return BaseResponse
     */
    public function getFilmReviews(int $filmId): BaseResponse
    {
        $this->filmRepository->findById($filmId);

        $reviews = $this->reviewRepository->allForFilm($filmId);

        return new SuccessResponse(
            data: $reviews
        );
    }
}
