<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GenreController extends Controller
{
    public function getGenres(): Response
    {
        //
        return new JsonResponse();
    }

    public function updateGenre(int $genreId): Response
    {
        //
        return new JsonResponse();
    }
}
