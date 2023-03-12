<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\HttpFoundation\Response;

class NotFoundResponse extends BaseFailResponse
{
    public function __construct(
        int $codeResponse = Response::HTTP_NOT_FOUND,
        ?string $message = 'This page is not found',
    ) {
        parent::__construct(
            codeResponse: $codeResponse,
            message: $message
        );
    }
}
