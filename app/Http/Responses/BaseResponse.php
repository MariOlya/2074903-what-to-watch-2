<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseResponse implements Responsable
{
    public function __construct(
        readonly int $codeResponse = Response::HTTP_OK,
        readonly ?string $message = null,
        readonly array|Arrayable $data = []
    )
    {
    }

    public function toResponse($request): Response
    {
        return response()->json($this->makeResponseData(), $this->codeResponse);
    }

    protected function prepareData(): array
    {
        return $this->data instanceof Arrayable ? $this->data->toArray() : $this->data;
    }

    abstract protected function makeResponseData(): array;
}
