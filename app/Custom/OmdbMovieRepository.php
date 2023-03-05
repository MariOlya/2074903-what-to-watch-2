<?php

declare(strict_types=1);

namespace App\Custom;

interface OmdbMovieRepository
{
    public function fetch(string $keyword) : array;
    public function get(string $uri) : array;
    public function output($code, $message, $data = null) : array;
}
