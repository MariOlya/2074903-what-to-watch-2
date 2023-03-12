<?php

declare(strict_types=1);

namespace App\Http\Responses;

class BaseFailResponse extends BaseResponse
{
    protected function makeResponseData(): array
    {
        return ['message' => $this->message];
    }
}
