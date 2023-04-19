<?php

declare(strict_types=1);

namespace App\Factories;

use App\Factories\Dto\ReviewDto;
use App\Factories\Interfaces\ReviewFactoryInterface;
use App\Models\Review;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class ReviewFactory implements ReviewFactoryInterface
{
    public function __construct(readonly Review $review)
    {
    }

    /**
     * @param int $filmId
     * @param int $userId
     * @param ReviewDto $reviewDto
     * @return Review
     * @throws InternalErrorException
     */
    public function createNewReview(ReviewDto $reviewDto): Review
    {
        $this->review->text = $reviewDto->getParams()['text'];
        $this->review->rating = $reviewDto->getParams()['rating'] ?? null;
        $this->review->review_id = $reviewDto->getParams()['comment_id'] ?? null;
        $this->review->film_id = $reviewDto->filmId;
        $this->review->user_id = $reviewDto->userId;

        if (!$this->review->save()) {
            throw new InternalErrorException('The error on the server, please, try again', 500);
        }

        return $this->review;
    }
}
