<?php

declare(strict_types=1);

namespace App\Factories\Interfaces;

use App\Models\Director;

interface DirectorFactoryInterface
{
    public function createNewDirector(string $name): Director;
}
