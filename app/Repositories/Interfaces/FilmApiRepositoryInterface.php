<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface FilmApiRepositoryInterface
{
    public function getMovieInfoById(string $id) : array;
}
