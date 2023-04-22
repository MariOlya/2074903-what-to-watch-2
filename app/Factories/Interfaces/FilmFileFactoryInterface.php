<?php

declare(strict_types=1);

namespace App\Factories\Interfaces;

interface FilmFileFactoryInterface
{
    public function createFromExternalApi(string $link, string $type, string $title): int;

    public function createFromEditForm(string $link, string $type): int;
}
