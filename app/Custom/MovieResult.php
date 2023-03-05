<?php

declare(strict_types=1);

namespace App\Custom;

use Illuminate\Support\Facades\App;

class MovieResult
{
    protected OmdbMovieRepository $movieRepository;

    public function __construct(OmdbMovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function getMovieInfo(string $keyword) : array
    {
        return $this->movieRepository->fetch($keyword);
    }
}
