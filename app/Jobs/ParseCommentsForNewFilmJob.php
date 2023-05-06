<?php

namespace App\Jobs;

use App\Factories\Dto\ReviewDto;
use App\Factories\ReviewFactory;
use App\Models\Film;
use App\Models\Review;
use App\Repositories\Interfaces\CommentsApiRepositoryInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

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
     * @throws InternalErrorException
     */
    public function handle(
        CommentsApiRepositoryInterface $commentsApiRepository,
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
            $commentsWithAllRequiredData = array_filter(
                $comments,
                static function ($comment) {
                    $imdbId = $comment['imdb_id'] ?? null;
                    $rating = $comment['rating'] ?? null;
                    $text = $comment['text'] ?? null;
                    return $imdbId !== null &&
                        $rating !== null &&
                        $text !== null;
                }
            );

            if (!empty($commentsWithAllRequiredData)) {
                DB::beginTransaction();

                try {
                    foreach ($commentsWithAllRequiredData as $comment) {
                        $newReviewDto = new ReviewDto(
                            text: $comment['text'],
                            rating: $comment['rating'],
                            filmId: $filmId,
                        );

                        (new ReviewFactory(new Review()))->createNewReview($newReviewDto);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
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
