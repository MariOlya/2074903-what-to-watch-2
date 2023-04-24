<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Factories\Dto\ReviewDto;
use App\Factories\Interfaces\ReviewFactoryInterface;
use App\Http\Requests\ReviewRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Review;
use App\Models\User;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function __construct(
        readonly ReviewFactoryInterface $reviewFactory,
        readonly FilmRepositoryInterface $filmRepository,
        readonly ReviewRepositoryInterface $reviewRepository
    )
    {
    }

    /**
     * POLICY: Only for auth users
     *
     * @param ReviewRequest $request
     * @param int $filmId
     * @return BaseResponse
     * @throws \Exception
     */
    public function addNewReview(ReviewRequest $request, int $filmId): BaseResponse
    {
        $params = $request->validated();
        /** @var User $user */
        $user = Auth::user();

        $reviewDto = new ReviewDto(
            text: $params['text'] ?? null,
            rating: $params['rating'] ?? null,
            reviewId: $params['comment_id'] ?? null,
            userId: $user->id,
            filmId: $filmId
        );

        $newVote = $reviewDto->rating;

        //Add review + update rating
        DB::beginTransaction();

        try {
            $newReview = $this->reviewFactory->createNewReview($reviewDto);

            if ($newVote) {
                $this->filmRepository->updateRating($filmId, $newVote);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return new SuccessResponse(
            data: $newReview
        );
    }

    /**
     * POLICY: User can change only own review, moderator can change any review
     *
     * @param ReviewRequest $request
     * @param Review $review
     * @return BaseResponse
     * @throws \Exception
     */
    public function updateReview(ReviewRequest $request, Review $review): BaseResponse
    {
        $params = $request->validated();
        $reviewDto = new ReviewDto(
            text: $params['text'] ?? null,
            rating: $params['rating'] ?? null,
            reviewId: $params['comment_id'] ?? null,
        );

        $reviewId = $review->id;
        $filmId = $review->film_id;

        $latestVote = $review->rating ?? null;
        $newVote = $reviewDto->rating;

        //Change review + update rating
        DB::beginTransaction();

        try {
            $updatedReview = $this->reviewRepository->update($reviewId, $reviewDto);

            if ($newVote) {
                $this->filmRepository->updateRating($filmId, $newVote, $latestVote);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return new SuccessResponse(
            data: $updatedReview
        );
    }

    /**
     * POLICY: User can delete only own review, moderator can delete any review
     *
     * @param Request $request
     * @param Review $review
     * @return BaseResponse
     * @throws AuthorizationException
     * @throws \Exception
     */
    public function deleteReview(Request $request, Review $review): BaseResponse
    {
        $this->authorize('delete', $review);

        $reviewId = $review->id;

        //Soft delete review + all child comments
        DB::beginTransaction();

        try {
            $this->reviewRepository->delete($reviewId);

            $this->reviewRepository->deleteChildReviews($reviewId);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return new SuccessResponse(
            data: ['This review was deleted successfully']
        );
    }
}
