<?php

namespace App\Http\Responses;

class BaseFailResponse extends BaseResponse
{
    protected function makeResponseData(): array
    {
        return ['error' => $this->message];
    }
}
