<?php

declare(strict_types=1);

namespace App\Factories\Interfaces;

use App\Models\File;

interface FilmFileFactoryInterface
{
    public function createFromExternalApi(string $link, string $type, string $title): File;

    public function createFromEditForm(string $link, string $type): int;
}
