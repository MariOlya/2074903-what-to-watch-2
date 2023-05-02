<?php

namespace App\Jobs;

use App\Factories\Dto\ReviewDto;
use App\Factories\Interfaces\ReviewFactoryInterface;
use App\Models\Film;
use App\Repositories\Interfaces\CommentsApiRepositoryInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParseCommentsForNewFilmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 10;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        readonly string $imdbId,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(
        CommentsApiRepositoryInterface $commentsApiRepository,
        ReviewFactoryInterface $reviewFactory,
    ): void {
        $response = $commentsApiRepository->getCommentsByFilmImdbId($this->imdbId);

        if ($response['code'] !== 200) {
            throw new \RuntimeException(
                message: $response['message'],
                code: $response['code']
            );
        }

        $comments = (array)$response['data'];
        $filmId = Film::whereImdbId($this->imdbId)->value('id');

        if (!empty($comments)) {
            foreach ($comments as $comment) {
                $newReviewDto = new ReviewDto(
                    text: $comment['text'] ?? null,
                    rating: $comment['rating'] ?? null,
                    filmId: $filmId,
                );
                if (
                    $newReviewDto->text !== null &&
                    $newReviewDto->rating !== null
                ) {
                    $reviewFactory->createNewReview($newReviewDto);
                }
            }
        }
    }

    /**
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception): void
    {
        Log::warning(
            'Your job to fill comments for new film ('.$this->imdbId.') did not work. There is exception: '.$exception->getMessage()
        );
    }
}
