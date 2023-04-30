<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Factories\Dto\UserDto;
use App\Factories\Interfaces\AvatarFactoryInterface;
use App\Factories\Interfaces\UserFactoryInterface;
use App\Http\Requests\UserRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\FileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(
        readonly UserFactoryInterface $userFactory,
        readonly UserRepositoryInterface $userRepository,
        readonly AvatarFactoryInterface $avatarFactory
    )
    {
    }

    /**
     * After fail validation the response returns as JSON response with 'message' and 'errors'
     *
     * @param UserRequest $request
     * @return BaseResponse
     * @throws \Exception
     */
    public function register(UserRequest $request): BaseResponse
    {
        $params = $request->safe()->except('file');
        $file = $request->safe()->only('file');

        DB::beginTransaction();
        try {

            if ($file) {
                $name = $file['file']->hashName();
                $newAvatar = $this->avatarFactory->createNewAvatar($name);
                $avatarFileName = substr($newAvatar->link, 8);
                $avatarId = $newAvatar->id;
            }

            $userDto = new UserDto(
                name: $params['name'] ?? null,
                email: $params['email'] ?? null,
                password: $params['password'] ?? null,
                fileId: $avatarId ?? null
            );

            $newUser = $this->userFactory->createNewUser($userDto);

            DB::commit();

            $request->file('file')?->storeAs(FileService::PUBLIC_STORAGE.'/avatars', $avatarFileName);

            $token = $newUser->createToken('auth-token');

            return new SuccessResponse(
                codeResponse: Response::HTTP_CREATED,
                data: [
                    'user' => $newUser,
                    'avatar' => $newAvatar->link ?? null,
                    'token' => $token->plainTextToken,
                ]
            );
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
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

        $userId = $user->id;
        $params = $request->safe()->except('file');
        $userDto = new UserDto(
            name: $params['name'] ?? null,
            email: $params['email'] ?? null,
            password: $params['password'] ?? null,
        );

        // add the rule here to save File and add file id to User
        // $fileParams = $request->safe()->only('file');

        $updatedUser = $this->userRepository->update($userId, $userDto);

        return new SuccessResponse(
            data: [
                'updatedUser' => $updatedUser,
//                'newAvatar' => $newAvatar ?? null
            ]
        );
    }
}
