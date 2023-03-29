<?php

namespace App\Repositories\Interfaces;

use App\Factories\Dto\Dto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function all(array $columns = ['*']): Collection;

    public function update(Dto $data, int $id): Model;

    public function delete(int $id): void;

    public function findById(int $id, array $columns = ['*']): ?Model;

    public function findBy(string $field, mixed $value, array $columns = ['*']): ?Model;
}
