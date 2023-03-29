<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

/**
 * Use this response for exceptions of /Error
 */
class ServerErrorResponse extends BaseFailResponse
{
    public function __construct(
        int $codeResponse = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?string $message = 'Internal server error',
    ) {
        parent::__construct(
            message: $message,
            codeResponse: $codeResponse
        );
    }
}
