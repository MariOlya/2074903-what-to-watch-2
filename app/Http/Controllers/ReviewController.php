<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Factories\Dto\ReviewDto;
use App\Factories\Interfaces\ReviewFactoryInterface;
use App\Http\Requests\ReviewRequest;
use App\Http\Responses\BadRequestResponse;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\NotFoundResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use App\Models\Film;
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
        $film = Film::whereId($filmId)->first();

        if (!$film) {
            return new NotFoundResponse();
        }

        $params = $request->validated();
        $newVote = $params['rating'] ?? null;

        /** @var User $user */
        $user = Auth::user();

        //Add review + update rating
        DB::beginTransaction();

        try {
            $newReview = $this->reviewFactory->createNewReview(
                new ReviewDto($params, $user->id, $filmId)
            );

            if ($newVote) {
                $this->filmRepository->updateRating($filmId, (int)$newVote);
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
     * @param Review $currentReview
     * @return BaseResponse
     * @throws \Exception
     */
    public function updateReview(ReviewRequest $request, Review $review): BaseResponse
    {
        /** @var Review $currentReview */
        $currentReview = $request->findReview();

        if (!$currentReview) {
            return new NotFoundResponse();
        }

        $params = $request->validated();

        $reviewId = $currentReview->id;
        $filmId = $currentReview->film_id;

        $latestVote = $currentReview->rating ?? null;
        $newVote = $params['rating'] ?? null;

        //Change review + update rating
        DB::beginTransaction();

        try {
            $updatedReview = $this->reviewRepository->update($reviewId, new ReviewDto($params));

            if ($newVote) {
                $this->filmRepository->updateRating($filmId, (int)$newVote, $latestVote);
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
     */
    public function deleteReview(Request $request, Review $review): BaseResponse
    {
        $this->authorize('delete', $review);

        /** @var Review $currentReview */
        $currentReview = Review::query()->find($request->route('comment'));

        if (!$currentReview) {
            return new NotFoundResponse();
        }

        $reviewId = $currentReview->id;

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
