<?php

declare(strict_types=1);

namespace App\Custom;

interface MovieInfoRepository
{
    public function getMovieInfoById(string $id) : array;
}
