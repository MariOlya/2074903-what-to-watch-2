<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;
class UnprocessableResponse extends BaseFailResponse
{
    public function __construct(
        int $codeResponse = Response::HTTP_UNPROCESSABLE_ENTITY,
        ?string $message = 'This action has already been done earlier',
    ) {
        parent::__construct(
            codeResponse: $codeResponse,
            message: $message
        );
    }
}
