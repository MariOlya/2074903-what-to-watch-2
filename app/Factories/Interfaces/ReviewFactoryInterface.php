<?php

declare(strict_types=1);

namespace App\Factories\Interfaces;

use App\Factories\Dto\ReviewDto;
use App\Models\Review;

interface ReviewFactoryInterface
{
    public function createNewReview(ReviewDto $reviewDto): Review;
}
