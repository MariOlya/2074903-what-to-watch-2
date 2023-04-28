<?php

namespace App\Factories\Dto;

class HtmlAcademyFilmApiDto extends Dto
{
    /**
     * @param string|null $title
     * @param string|null $previewImage
     * @param string|null $backgroundImage
     * @param string|null $videoLink
     * @param string|null $previewVideoLink
     */
    public function __construct(
        readonly ?string $title,
        readonly ?string $previewImage,
        readonly ?string $backgroundImage,
        readonly ?string $videoLink,
        readonly ?string $previewVideoLink
    )
    {
    }
}
