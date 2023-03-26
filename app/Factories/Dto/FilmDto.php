<?php

namespace App\Factories\Dto;

class FilmDto extends Dto
{
    public function __construct(
        readonly array $params,
        readonly array $fileIds,
        readonly int $directorId,
        readonly array $linkIds,
    )
    {
        parent::__construct(params: $this->params);
    }
}
