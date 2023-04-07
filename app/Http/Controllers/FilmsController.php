<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\NotFoundResponse;
use App\Http\Responses\PaginatedSuccessResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use App\Models\Film;
use App\Models\User;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilmsController extends Controller
{
    public function __construct(
        readonly FilmRepositoryInterface $filmRepository
    )
    {
    }

    /**
     * POLICY: Only moderator can ask films in another status (pending, moderate)
     *
     * @param Request $request
     * @return BaseResponse
     */
    public function getFilms(Request $request): BaseResponse
    {
        $queryParams = $request->all();
        /** @var User $currentUser */
        $currentUser = $request->user('sanctum');

        if (
            isset($queryParams['status']) &&
            $queryParams['status'] !== Film::FILM_DEFAULT_STATUS &&
            ($currentUser === null ||
            $currentUser->userRole->role === User::ROLE_DEFAULT)
        ) {
            $queryParams['status'] = Film::FILM_DEFAULT_STATUS;
        }

        $paginatedListFilms = $this->filmRepository->paginateList($queryParams);
        return new PaginatedSuccessResponse(
            data: $paginatedListFilms
        );
    }

    /**
     * POLICY: Only own favorite films
     *
     * @return BaseResponse
     */
    public function getFavoriteFilms(): BaseResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return new UnauthorizedResponse();
        }

        $userId = $user->id;

        $paginatedFavoriteFilms = $this->filmRepository->favoriteFilms($userId);
        return new PaginatedSuccessResponse(
            data: $paginatedFavoriteFilms
        );
    }

    /**
     * POLICY: Free for all
     *
     * @param int $filmId
     * @return BaseResponse
     */
    public function getSimilarFilms(int $filmId): BaseResponse
    {
        $films = $this->filmRepository->similarFilms($filmId);

        if (!$films) {
            return new NotFoundResponse();
        }
        return new SuccessResponse(data: $films);
    }
}
