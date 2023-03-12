<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilmController extends Controller
{
    public function getFilmInfo(int $filmId): Response
    {
        //
        return new JsonResponse();
    }

    public function addFavoriteFilm(int $filmId): Response
    {
        //
        return new JsonResponse();
    }

    public function deleteFavoriteFilm(int $filmId): Response
    {
        //
        return new JsonResponse();
    }

    public function addNewFilm(): Response
    {
        //
        return new JsonResponse();
    }

    public function updateFilm(int $filmId): Response
    {
        //
        return new JsonResponse();
    }

    public function getFilmComments(int $filmId): Response
    {
        //
        return new JsonResponse();
    }
}
