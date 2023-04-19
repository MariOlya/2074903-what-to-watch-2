<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\NoContentResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class AuthenticationController extends Controller
{
    public function login(LoginRequest $request): BaseResponse
    {
        try {
            if (!Auth::attempt($request->validated())) {
                throw new UnauthorizedException();
            }

            /** @var User $user */
            $user = $request->user();

            $token = $user->createToken('auth-token');

            return new SuccessResponse(
                data: [
                    'token' => $token->plainTextToken,
                    'user' => $user
                ]
            );
        } catch (\Throwable) {
            return new UnauthorizedResponse();
        }
    }

    public function logout(Request $request): BaseResponse
    {
        $request->user()->tokens()->delete();

        return new NoContentResponse();
    }
}
