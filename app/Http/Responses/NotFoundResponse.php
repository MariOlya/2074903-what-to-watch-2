<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

class NotFoundResponse extends BaseFailResponse
{
    public function __construct(
        int $codeResponse = Response::HTTP_NOT_FOUND,
        ?string $message = 'This page is not found',
    ) {
        parent::__construct(
            message: $message,
            codeResponse: $codeResponse
        );
    }
}
