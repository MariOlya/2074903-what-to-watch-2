<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Factories\Dto\UserDto;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface
{
    public function update(int $id, UserDto $dto): Model;
}
