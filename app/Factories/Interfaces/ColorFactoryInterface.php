<?php

declare(strict_types=1);

namespace App\Factories\Interfaces;

use App\Models\Color;

interface ColorFactoryInterface
{
    public function createNewColor(string $color): Color;
}
