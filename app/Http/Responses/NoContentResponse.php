<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

class NoContentResponse extends SuccessResponse
{
    public function __construct(
        int $codeResponse = Response::HTTP_NO_CONTENT
    ) {
        parent::__construct(
            codeResponse: $codeResponse,
        );
    }
}
