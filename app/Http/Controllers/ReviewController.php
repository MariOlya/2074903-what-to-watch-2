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
     * @param int $reviewId
     * @return BaseResponse
     */
    public function updateReview(ReviewRequest $request, int $reviewId): BaseResponse
    {
        /** @var Review $review */
        $review = $request->findReview();

        if (!$review) {
            return new NotFoundResponse();
        }

        $params = $request->validated();

        $filmId = $review->film_id;
        $latestVote = $review->rating ?? null;
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

    public function deleteReview(int $reviewId): BaseResponse
    {
        //there will be check of this review, but we set now 'mock'
        if (!$reviewId) {
            return new NotFoundResponse();
        }

        //there will be check that the user tried to do this with his/her review is logged, but we set now 'mock'
        if ($reviewId === 1) {
            return new UnauthorizedResponse();
        }

        //there will be check that the user tried to delete any (not his/her) review is logged and moderator,
        //but we set now 'mock'
        if ($reviewId === 2) {
            return new BadRequestResponse();
        }

        return new SuccessResponse();
    }
}
