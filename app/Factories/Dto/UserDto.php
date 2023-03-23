<?php

declare(strict_types=1);

namespace App\Factories\Dto;

use App\Http\Requests\UserRegisterRequest;

class UserDto
{
    public function __construct(
        readonly array $params,
        readonly ?int $fileId = null
    )
    {
    }
}
