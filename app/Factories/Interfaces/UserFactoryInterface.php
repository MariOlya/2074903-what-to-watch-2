<?php

declare(strict_types=1);

namespace App\Factories\Interfaces;

use App\Factories\Dto\UserDto;
use App\Models\User;

interface UserFactoryInterface
{
    public function createNewUser(UserDto $userDto): User;
}
