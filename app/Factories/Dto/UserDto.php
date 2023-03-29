<?php

declare(strict_types=1);

namespace App\Factories\Dto;


class UserDto extends Dto
{
    /**
     * @param array $params Includes 'name', 'email', 'password'
     * @param int|null $fileId
     */
    public function __construct(
        array $params,
        readonly ?int $fileId = null
    )
    {
        $this->setParams($params);
    }
}
