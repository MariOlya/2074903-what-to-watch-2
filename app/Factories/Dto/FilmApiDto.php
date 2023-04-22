<?php

namespace App\Factories\Dto;

class FilmApiDto extends Dto
{
    /**
     * @param array $params Includes 'Title', 'Year', 'Rated', 'Released', 'Runtime', 'Genre', 'Director',
     * 'Writer', 'Actors', 'Plot', 'Language', 'Country', 'Awards', 'Poster', 'Ratings[]', 'Metascore',
     * 'imdbRating', 'imdbVotes', 'imdbID', 'Type', 'DVD', 'BoxOffice', 'Production', 'Website', 'Response'
     */
    public function __construct(
        array $params,
    )
    {
        $this->setParams($params);
    }
}
