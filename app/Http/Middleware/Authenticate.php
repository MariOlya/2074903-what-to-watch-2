<?php

namespace App\Http\Middleware;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\UnauthorizedResponse;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): string|BaseResponse
    {
        return $request->expectsJson() ? new UnauthorizedResponse() : route('login');
    }
}
