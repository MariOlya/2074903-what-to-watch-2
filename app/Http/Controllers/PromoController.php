<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PromoController extends Controller
{
    public function getPromoFilm(): Response
    {
        //
        return new JsonResponse();
    }

    public function setPromoFilm(int $filmId): Response
    {
        //
        return new JsonResponse();
    }
}
