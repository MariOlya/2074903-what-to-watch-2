<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\BadRequestResponse;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\NotFoundResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function addNewReview(Request $request, int $filmId): BaseResponse
    {
        //there will be check of this film, but we set now 'mock'
        if (!$filmId) {
            return new NotFoundResponse();
        }

        //there will be check that the user tried to do this is logged, but we set now 'mock'
        if ($filmId === 2) {
            return new UnauthorizedResponse();
        }

        return new SuccessResponse();
    }

    public function updateReview(Request $request, int $reviewId): BaseResponse
    {
        //there will be check of this review, but we set now 'mock'
        if (!$reviewId) {
            return new NotFoundResponse();
        }

        //there will be check that the user tried to do this with his/her review is logged, but we set now 'mock'
        if ($reviewId === 1) {
            return new UnauthorizedResponse();
        }

        //there will be check that the user tried to update any (not his/her) review is logged and moderator,
        //but we set now 'mock'
        if ($reviewId === 2) {
            return new BadRequestResponse();
        }

        return new SuccessResponse();
    }

    public function deleteReview(int $reviewId): BaseResponse
    {
        //there will be check of this review, but we set now 'mock'
        if (!$reviewId) {
            return new NotFoundResponse();
        }

        //there will be check that the user tried to do this with his/her review is logged, but we set now 'mock'
        if ($reviewId === 1) {
            return new UnauthorizedResponse();
        }

        //there will be check that the user tried to delete any (not his/her) review is logged and moderator,
        //but we set now 'mock'
        if ($reviewId === 2) {
            return new BadRequestResponse();
        }

        return new SuccessResponse();
    }
}
