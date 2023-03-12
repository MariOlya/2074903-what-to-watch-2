<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function getGenres(): BaseResponse
    {
        //
        return new SuccessResponse();
    }

    public function updateGenre(int $genreId): BaseResponse
    {
        //there will be check that the user tried to do this is logged and moderator, but we set now 'mock'
        try {
            return new SuccessResponse();
        } catch (\Throwable) {
            return new UnauthorizedResponse();
        }
    }
}
