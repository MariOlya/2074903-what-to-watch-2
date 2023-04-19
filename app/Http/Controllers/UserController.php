<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Factories\Dto\UserDto;
use App\Factories\Interfaces\UserFactoryInterface;
use App\Http\Requests\UserRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(
        readonly UserFactoryInterface $userFactory,
        readonly UserRepositoryInterface $userRepository
    )
    {
    }

    /**
     * After fail validation the response returns as JSON response with 'message' and 'errors'
     *
     * @param UserRequest $request
     * @return BaseResponse
     */
    public function register(UserRequest $request): BaseResponse
    {
        $params = $request->safe()->except('file');

        // add the rule here to save File and add file id to User
        // $fileParams = $request->safe()->only('file');

        $newUser = $this->userFactory->createNewUser(new UserDto($params));

        $token = $newUser->createToken('auth-token');

        return new SuccessResponse(
            codeResponse: Response::HTTP_CREATED,
            data: [
                'user' => $newUser,
                'token' => $token->plainTextToken,
            ]
        );
    }

    /**
     * POLICY: Only own profile
     *
     * @return BaseResponse
     */
    public function getUser(): BaseResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return new UnauthorizedResponse();
        }

        $avatar = $user->avatar;

        return new SuccessResponse(
            data: [
                'user' => $user,
                'avatar' => $avatar
            ]
        );
    }

    /**
     * POLICY: Only own profile
     *
     * @param UserRequest $request
     * @return BaseResponse
     */
    public function updateUser(UserRequest $request): BaseResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return new UnauthorizedResponse();
        }

        $userId = $user->id;
        $params = $request->safe()->except('file');

        // add the rule here to save File and add file id to User
        // $fileParams = $request->safe()->only('file');

        $updatedUser = $this->userRepository->update($userId, new UserDto($params));

        return new SuccessResponse(
            data: [
                'updatedUser' => $updatedUser,
//                'newAvatar' => $newAvatar ?? null
            ]
        );
    }
}
