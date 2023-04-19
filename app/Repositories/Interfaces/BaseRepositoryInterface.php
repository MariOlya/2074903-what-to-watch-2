<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Factories\Dto\Dto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public const DEFAULT_LIMIT = 100;
    public const DEFAULT_OFFSET = 0;

    public function all(array $columns = ['*'], int $limit = self::DEFAULT_LIMIT, int $offset = self::DEFAULT_OFFSET): Collection;

    public function update(int $id, Dto $dto): Model;

    public function delete(int $id): void;

    public function findById(int $id, array $columns = ['*']): ?Model;

    public function findBy(string $field, mixed $value, array $columns = ['*']): ?Model;
}
