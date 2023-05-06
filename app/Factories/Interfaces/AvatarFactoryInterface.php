<?php

declare(strict_types=1);

namespace App\Factories\Interfaces;

use App\Models\File;

interface AvatarFactoryInterface
{
    public function createNewAvatar(string $name): File;
}
