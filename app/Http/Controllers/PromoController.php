<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\NotFoundResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use App\Models\Film;
use App\Models\User;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use Illuminate\Http\Request;

class PromoController extends Controller
{

    public function __construct(
        readonly FilmRepositoryInterface $filmRepository
    )
    {
    }

    public function getPromoFilm(Request $request): BaseResponse
    {
        /** @var Film $film */
        $film = $this->filmRepository->findPromo();
        $filmId = $film->id;

        /** @var User $currentUser */
        $currentUser = $request->user('sanctum');

        if ($currentUser) {
            $isFavorite = (bool)$currentUser->favoriteFilms()->where('film_id', '=', $filmId)->first();
            $film = $film->toArray();
            $film['is_favorite'] = $isFavorite;
        }

        return new SuccessResponse(data: $film);
    }

    public function setPromoFilm(Request $request, int $filmId): BaseResponse
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
}
