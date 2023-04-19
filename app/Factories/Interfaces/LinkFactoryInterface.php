<?php

declare(strict_types=1);

namespace App\Factories\Interfaces;

interface LinkFactoryInterface
{
    public function createNewLink(string $link, string $type): int;
}
