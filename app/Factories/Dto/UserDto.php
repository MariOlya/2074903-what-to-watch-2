<?php

declare(strict_types=1);

namespace App\Factories\Dto;


class UserDto extends Dto
{
    /**
     * @param string|null $name
     * @param string|null $email
     * @param string|null $password
     * @param int|null $fileId
     */
    public function __construct(
        readonly ?string $name,
        readonly ?string $email,
        readonly ?string $password,
        readonly ?int $fileId = null
    )
    {
    }
}
