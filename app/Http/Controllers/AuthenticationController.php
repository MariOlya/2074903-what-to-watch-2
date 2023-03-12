<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\BadRequestResponse;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\NoContentResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function login(Request $request): BaseResponse
    {
        try {
            //
            return new SuccessResponse();
        } catch (\Throwable) {
            return new BadRequestResponse();
        }
    }

    public function logout(Request $request): BaseResponse
    {
        //
        return new NoContentResponse();
    }
}
