<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Factories\Dto\UserDto;
use App\Factories\Interfaces\UserFactoryInterface;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Responses\BaseFailResponse;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * After fail validation the response returns as JSON response with 'message' and 'errors'
     *
     * @param UserRegisterRequest $request
     * @param UserFactoryInterface $userFactory
     * @return BaseResponse
     */
    public function register(UserRegisterRequest $request, UserFactoryInterface $userFactory): BaseResponse
    {
        try {
            $request->safe()->except('file');

            // add the rule here to save File and add file id to User
            // $fileParams = $request->safe()->only('file');

            $newUser = $userFactory->createNewUser(new UserDto($request));

            $token = $newUser->createToken('auth-token');

            return new SuccessResponse(
                codeResponse: Response::HTTP_CREATED,
                data: [
                    'user' => $newUser,
                    'token' => $token->plainTextToken,
                ]
            );
        } catch (\Throwable $e) {
            return new BaseFailResponse(exception: $e);
        }
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
