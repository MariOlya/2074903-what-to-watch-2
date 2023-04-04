<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Film;
use App\Models\Genre;
use App\Repositories\Interfaces\GenreRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class GenreRepository implements GenreRepositoryInterface
{

    public function all(array $columns = ['*'], int $limit = 100, int $offset = 0): Collection
    {
        return Genre::query()->limit($limit)->offset($offset)->get($columns);
    }

    public function update(int $id, string $genre): Model
    {
        $updatedGenre = Genre::query()->find($id);

        if ($genre !== $updatedGenre->genre) {
            $updatedGenre->genre = $genre;
        }

        $updatedGenre->update();

        return $updatedGenre;

    }
}
