<?php

declare(strict_types=1);

namespace App\Factories\Dto;
abstract class Dto
{
    public function __construct(
        readonly array $params,
    )
    {
    }
}
