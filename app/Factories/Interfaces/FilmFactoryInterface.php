<?php

declare(strict_types=1);

namespace App\Factories\Interfaces;

use App\Models\Film;

interface FilmFactoryInterface
{
    public function createNewFilm(string $imdbId): Film;
}
