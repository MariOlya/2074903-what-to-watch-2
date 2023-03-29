<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Use this response for exceptions of /Exception
 */
class BaseFailResponse extends BaseResponse
{
    public function __construct(
        ?string $message = null,
        int $codeResponse = Response::HTTP_BAD_REQUEST,
        ?\Throwable $exception = null
    ) {
        parent::__construct(
            codeResponse: $codeResponse,
            message: $exception?->getMessage() ?? $message
        );
    }

    protected function makeResponseData(): array
    {
        return ['message' => $this->message];
    }
}
