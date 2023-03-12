<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function register(Request $request): BaseResponse
    {
        //
        return new SuccessResponse();
    }

    public function getUser(): BaseResponse
    {
        //there will be check that the user tried to do this is logged, but we set now 'mock'
        try {
            return new SuccessResponse();
        } catch (\Throwable) {
            return new UnauthorizedResponse();
        }
    }

    public function updateUser(Request $request): BaseResponse
    {
        //there will be check that the user tried to do this is logged, but we set now 'mock'
        try {
            return new SuccessResponse();
        } catch (\Throwable) {
            return new UnauthorizedResponse();
        }
    }
}
