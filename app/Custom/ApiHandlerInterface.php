<?php

declare(strict_types=1);

namespace App\Custom;

interface ApiHandlerInterface
{
    public function fetch(string $requiredKeyword = null): string|array;
}
