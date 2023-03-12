<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\NotFoundResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function getPromoFilm(): BaseResponse
    {
        //
        return new SuccessResponse();
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
