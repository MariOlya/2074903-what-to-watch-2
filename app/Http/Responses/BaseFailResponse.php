<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class BaseFailResponse extends BaseResponse
{
    public function __construct(
        ?string $message = null,
        int $codeResponse = Response::HTTP_OK,
        ?\Throwable $exception = null
    ) {
        parent::__construct(
            codeResponse: $exception?->getCode() ?? $codeResponse,
            message: $exception?->getMessage() ?? $message
        );
    }

    protected function makeResponseData(): array
    {
        return ['message' => $this->message];
    }
}
