<?php

declare(strict_types=1);

namespace App\Factories;

use App\Factories\Interfaces\FilmFactoryInterface;
use App\Models\Film;
use App\Models\FilmStatus;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class FilmFactory implements FilmFactoryInterface
{
    public function __construct(readonly Film $film)
    {
    }

    /**
     * @param string $imdbId
     * @return Film
     * @throws InternalErrorException
     */
    public function createNewFilm(string $imdbId): Film
    {
        $this->film->imdb_id = $imdbId;
        $this->film->status_id = FilmStatus::whereStatus(Film::NEW_FILM_STATUS)->value('id');

        if (!$this->film->save()) {
            throw new InternalErrorException('The error on the server, please, try again', 500);
        }

        return $this->film;
    }
}
