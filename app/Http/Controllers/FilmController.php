<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\NotFoundResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use App\Http\Responses\UnprocessableResponse;
use App\Models\Film;
use App\Models\User;
use App\Repositories\FilmRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilmController extends Controller
{
    public function __construct(
        readonly FilmRepository $filmRepository
    )
    {
    }

    public function getFilmInfo(int $filmId): BaseResponse
    {
        $film = $this->filmRepository->findById($filmId);

        if (!$film) {
            return new NotFoundResponse();
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user) {
            $isFavorite = (bool)$user->favoriteFilms()->where('id', '=', $filmId)->first('id');
            $film = $film->toArray();
            $film['is_favorite'] = $isFavorite;
        }

        return new SuccessResponse(data: $film);
    }

    public function addFavoriteFilm(Request $request, int $filmId): BaseResponse
    {
        //there will be check of this film, but we set now 'mock'
        if (!$filmId) {
            return new NotFoundResponse();
        }

        //there will be check that the film isn't in favorite already, but we set now 'mock'
        if ($filmId === 1) {
            return new UnprocessableResponse();
        }

        //there will be check that the user tried to do this is logged, but we set now 'mock'
        if ($filmId === 2) {
            return new UnauthorizedResponse();
        }

        return new SuccessResponse();
    }

    public function deleteFavoriteFilm(int $filmId): BaseResponse
    {
        //there will be check of this film, but we set now 'mock'
        if (!$filmId) {
            return new NotFoundResponse();
        }

        //there will be check that the film is in favorite now, but we set now 'mock'
        if ($filmId === 1) {
            return new UnprocessableResponse();
        }

        //there will be check that the user tried to do this is logged, but we set now 'mock'
        if ($filmId === 2) {
            return new UnauthorizedResponse();
        }

        return new SuccessResponse();
    }

    public function addNewFilm(Request $request): BaseResponse
    {
        //there will be check that the new id is not in db already, but we set now 'mock'
        try {
            return new SuccessResponse();
        } catch (\Throwable) {
            return new UnprocessableResponse();
        }
    }

    public function updateFilm(Request $request, int $filmId): BaseResponse
    {
        //there will be check of this film, but we set now 'mock'
        if (!$filmId) {
            return new NotFoundResponse();
        }

        //there will be check that the user tried to do this is logged and moderator, but we set now 'mock'
        if ($filmId === 2) {
            return new UnauthorizedResponse();
        }

        return new SuccessResponse();
    }

    public function getFilmComments(int $filmId): BaseResponse
    {
        //there will be check of this film, but we set now 'mock'
        if (!$filmId) {
            return new NotFoundResponse();
        }
        return new SuccessResponse();
    }
}
