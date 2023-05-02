<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface CommentsApiRepositoryInterface
{
    public function getCommentsByFilmImdbId(string $imdbId): array;

    public function getAllNewComments(): array;
}
