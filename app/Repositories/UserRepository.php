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
        $updatedUser = User::whereId($id)->firstOrFail();

        if ($dto->name && $dto->name !== $updatedUser->name) {
            $updatedUser->name = $dto->name;
        }

        if ($dto->email && $dto->email !== $updatedUser->email) {
            $updatedUser->email = $dto->email;
        }

        if ($dto->password) {
            $hashedPassword = Hash::make($dto->password);
            if ($hashedPassword !== $updatedUser->password) {
                $updatedUser->password = $hashedPassword;
            }
        }

        if ($dto->fileId) {
            $updatedUser->avatar_id = $dto->fileId;
        }

        $updatedUser->update();

        return $updatedUser;
    }
}
