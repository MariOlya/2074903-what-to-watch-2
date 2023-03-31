<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface GenreRepositoryInterface
{
    public function all(array $columns = ['*'], int $limit = 100, int $offset = 0): Collection;

    public function update(int $id, string $genre): Model;
}
