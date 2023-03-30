<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Factories\Dto\UserDto;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{

    public function update(int $id, UserDto $dto): Model
    {
        $updatedUser = User::query()->find($id);

        $newName = $dto->getParams()['name'] ?? null;
        $newPassword = $dto->getParams()['password'] ?? null;
        $newEmail = $dto->getParams()['email'] ?? null;
        $newAvatarId = $dto->fileId;

        if ($newName && $newName !== $updatedUser->name) {
            $updatedUser->name = $newName;
        }

        if ($newEmail && $newEmail !== $updatedUser->email) {
            $updatedUser->email = $newEmail;
        }

        if ($newPassword) {
            $hashedPassword = Hash::make($newPassword);
            if ($hashedPassword !== $updatedUser->password) {
                $updatedUser->password = $hashedPassword;
            }
        }

        if ($newAvatarId) {
            $updatedUser->avatar_id = $newAvatarId;
        }

        $updatedUser->update();

        return $updatedUser;
    }
}
