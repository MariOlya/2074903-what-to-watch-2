<?php

declare(strict_types=1);

namespace App\Factories\Dto;

class ReviewDto extends Dto
{
    /**
     * @param array $params Can includes 'text', 'rating', 'comment_id'
     */
    public function __construct(
        array $params,
        readonly ?int $userId = null,
        readonly ?int $filmId = null

    )
    {
        $this->setParams($params);
    }
}
