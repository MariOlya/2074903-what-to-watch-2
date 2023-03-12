<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilmsController extends Controller
{
    public function getFilms(): Response
    {
        //
        return new JsonResponse();
    }

    public function getFavoriteFilms(): Response
    {
        //
        return new JsonResponse();
    }

    public function getSimilarFilms(int $filmId): Response
    {
        //
        return new JsonResponse();
    }
}
