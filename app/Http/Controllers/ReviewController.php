<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    public function addNewReview(int $filmId): Response
    {
        //
        return new JsonResponse();
    }

    public function updateReview(int $reviewId): Response
    {
        //
        return new JsonResponse();
    }

    public function deleteReview(int $reviewId): Response
    {
        //
        return new JsonResponse();
    }
}
