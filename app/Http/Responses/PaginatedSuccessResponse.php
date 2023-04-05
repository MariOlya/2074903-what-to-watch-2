<?php

declare(strict_types=1);

namespace App\Http\Responses;

class PaginatedSuccessResponse extends BaseResponse
{
    protected function makeResponseData(): array
    {
        return $this->prepareData();
    }
}
