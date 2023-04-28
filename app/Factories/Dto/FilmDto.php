<?php

declare(strict_types=1);

namespace App\Factories\Dto;

class FilmDto extends Dto
{
    /**
     * @param string|null $name
     * @param string|null $posterImage
     * @param string|null $previewImage
     * @param string|null $backgroundImage
     * @param string|null $backgroundColor
     * @param string|null $videoLink
     * @param string|null $previewVideoLink
     * @param string|null $description
     * @param string|null $director
     * @param array|null $actors
     * @param array|null $genres
     * @param int|null $runTime
     * @param int|null $released
     * @param string|null $imdbId
     * @param string|null $status
     */
    public function __construct(
        readonly ?string $name,
        readonly ?string $posterImage,
        readonly ?string $previewImage,
        readonly ?string $backgroundImage,
        readonly ?string $backgroundColor,
        readonly ?string $videoLink,
        readonly ?string $previewVideoLink,
        readonly ?string $description,
        readonly ?string $director,
        readonly ?array $actors,
        readonly ?array $genres,
        readonly ?int $runTime,
        readonly ?int $released,
        readonly ?string $imdbId,
        readonly ?string $status
    )
    {
    }
}
