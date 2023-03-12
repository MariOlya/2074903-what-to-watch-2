<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

class UnauthorizedResponse extends BaseFailResponse
{
    public function __construct(
        int $codeResponse = Response::HTTP_UNAUTHORIZED,
        ?string $message = 'You are not logged in or you do not have permission to this page',
    ) {
        parent::__construct(
            codeResponse: $codeResponse,
            message: $message
        );
    }
}
