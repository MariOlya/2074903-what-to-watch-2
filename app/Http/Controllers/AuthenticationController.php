<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function login(): Response
    {
        //
        return new JsonResponse();
    }

    public function logout(): Response
    {
        //
        return new JsonResponse();
    }
}
