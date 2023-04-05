<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\NotFoundResponse;
use App\Http\Responses\PaginatedSuccessResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use Illuminate\Http\Request;

class FilmsController extends Controller
{
    public function __construct(
        readonly FilmRepositoryInterface $filmRepository
    )
    {
    }

    public function getFilms(Request $request): BaseResponse
    {
        $queryParams = $request->all();
        $paginatedListFilms = $this->filmRepository->paginateList($queryParams);
        return new PaginatedSuccessResponse(
            data: $paginatedListFilms
        );
    }

    public function getFavoriteFilms(): BaseResponse
    {
        //there will be check that the user is logged, but we set now 'mock'
        try {
            return new SuccessResponse();
        } catch (\Throwable) {
            return new UnauthorizedResponse();
        }
    }

    public function getSimilarFilms(int $filmId): BaseResponse
    {
        //there will be check of this film, but we set now 'mock'
        if (!$filmId) {
            return new NotFoundResponse();
        }
        return new SuccessResponse();
    }
}
