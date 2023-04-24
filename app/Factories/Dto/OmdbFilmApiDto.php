<?php

namespace App\Factories\Dto;

class OmdbFilmApiDto extends Dto
{
    /**
     * @param string|null $title
     * @param string|null $released
     * @param string|null $runTime
     * @param string|null $genres
     * @param string|null $director
     * @param string|null $actors
     * @param string|null $description
     * @param string|null $posterImage
     * @param string|null $rating
     * @param string|null $amountVotes
     */
    public function __construct(
        readonly ?string $title,
        readonly ?string $released,
        readonly ?string $runTime,
        readonly ?string $genres,
        readonly ?string $director,
        readonly ?string $actors,
        readonly ?string $description,
        readonly ?string $posterImage,
        readonly ?string $rating,
        readonly ?string $amountVotes,
    )
    {
    }
}
