<?php

declare(strict_types=1);

namespace App\Factories\Dto;

abstract class Dto
{
    protected array $params;

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }


}
