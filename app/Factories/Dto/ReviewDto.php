<?php

declare(strict_types=1);

namespace App\Factories\Dto;

class ReviewDto extends Dto
{
    /**
     * @param string|null $text
     * @param int|null $rating
     * @param int|null $reviewId
     * @param int|null $userId
     * @param int|null $filmId
     */
    public function __construct(
        readonly ?string $text,
        readonly ?int $rating,
        readonly ?int $filmId,
        readonly ?int $reviewId = null,
        readonly ?int $userId = null,
    )
    {
    }
}
