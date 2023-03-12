<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

class BadRequestResponse extends BaseFailResponse
{
    public function __construct(
        int $codeResponse = Response::HTTP_BAD_REQUEST,
        ?string $message = 'The page is does not exist',
    ) {
        parent::__construct(
            codeResponse: $codeResponse,
            message: $message
        );
    }
}
