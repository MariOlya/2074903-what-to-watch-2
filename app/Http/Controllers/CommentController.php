<?php

namespace App\Http\Controllers;

use App\Http\Responses\BadRequestResponse;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\NotFoundResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function addNewComment(Request $request, int $filmId, int $reviewId): BaseResponse
    {
        //there will be check of this film, but we set now 'mock'
        if (!$filmId) {
            return new NotFoundResponse();
        }

        //there will be check that the user tried to do this is logged, but we set now 'mock'
        if ($filmId === 2) {
            return new UnauthorizedResponse();
        }

        //Add review + update rating
        DB::transaction(static function () use ($filmId) {
        }, 5);


        return new SuccessResponse();
    }

    public function updateComment(Request $request, int $commentId): BaseResponse
    {
        //there will be check of this review, but we set now 'mock'
        if (!$commentId) {
            return new NotFoundResponse();
        }

        //there will be check that the user tried to do this with his/her review is logged, but we set now 'mock'
        if ($commentId === 1) {
            return new UnauthorizedResponse();
        }

        //there will be check that the user tried to update any (not his/her) review is logged and moderator,
        //but we set now 'mock'
        if ($commentId === 2) {
            return new BadRequestResponse();
        }

        return new SuccessResponse();
    }

    public function deleteComment(int $commentId): BaseResponse
    {
        //there will be check of this review, but we set now 'mock'
        if (!$commentId) {
            return new NotFoundResponse();
        }

        //there will be check that the user tried to do this with his/her review is logged, but we set now 'mock'
        if ($commentId === 1) {
            return new UnauthorizedResponse();
        }

        //there will be check that the user tried to delete any (not his/her) review is logged and moderator,
        //but we set now 'mock'
        if ($commentId === 2) {
            return new BadRequestResponse();
        }

        return new SuccessResponse();
    }
}
