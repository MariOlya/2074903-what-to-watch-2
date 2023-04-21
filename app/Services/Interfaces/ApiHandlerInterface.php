<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface ApiHandlerInterface
{
    public function fetch(string $requiredKeyword = null): string|array;
}
