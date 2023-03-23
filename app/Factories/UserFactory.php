<?php

declare(strict_types=1);

namespace App\Factories;

use App\Factories\Dto\UserDto;
use App\Factories\Interfaces\UserFactoryInterface;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class UserFactory implements UserFactoryInterface
{
    public function __construct(readonly User $user)
    {
    }

    /**
     * @param UserDto $userDto
     * @return User
     * @throws InternalErrorException
     */
    public function createNewUser(UserDto $userDto): User
    {
        $hashedPassword = Hash::make($userDto->params->password);

        $this->user->password = $hashedPassword;
        $this->user->name = $userDto->params->name;
        $this->user->email = $userDto->params->email;
        $this->user->user_role_id = UserRole::whereRole(User::ROLE_DEFAULT)->value('id');

        if ($userDto->fileId) {
            $this->user->avatar_id = $userDto->fileId;
        }

        if (!$this->user->save()) {
            throw new InternalErrorException('The error on the server, please, try again', 500);
        }

        return $this->user ;
    }
}
