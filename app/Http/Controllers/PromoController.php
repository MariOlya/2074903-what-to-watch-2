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
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromoController extends Controller
{

    public function __construct(
        readonly FilmRepositoryInterface $filmRepository
    )
    {
    }

    /**
     * POLICY: For user we need to add isFavorite
     *
     * @param Request $request
     * @return BaseResponse
     */
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

    /**
     * @param Request $request
     * @param int $filmId
     * @return BaseResponse
     * @throws AuthorizationException
     */
    public function setPromoFilm(Request $request, int $filmId): BaseResponse
    {
        /** @var Film $currentFilm */
        $currentFilm = $this->filmRepository->findById($filmId);
        $this->authorize('setPromo', $currentFilm);

        DB::beginTransaction();
        try {
            $currentFilm->update(['promo' => true]);
            if ($previousPromoFilm = $this->filmRepository->findPromo()) {
                $previousPromoFilm->update(['promo' => false]);
            }
            DB::commit();
            return new SuccessResponse(data: $currentFilm);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
