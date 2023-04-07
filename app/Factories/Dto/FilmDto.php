<?php

namespace App\Factories\Dto;

class FilmDto extends Dto
{
    public function __construct(
        array $params,
        readonly array $fileIds,
        readonly int $directorId,
        readonly array $linkIds,
    )
    {
        $this->setParams($params);
    }
}
