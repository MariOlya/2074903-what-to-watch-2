<?php

declare(strict_types=1);

namespace App\Http\Responses;

class SuccessResponse extends BaseResponse
{
    protected function makeResponseData(): array
    {
        return ['data' => $this->prepareData()];
    }
}
