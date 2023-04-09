<?php

namespace App\Factories;

use App\Factories\Interfaces\ColorFactoryInterface;
use App\Models\Color;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class ColorFactory implements ColorFactoryInterface
{
    public function __construct(readonly Color $color)
    {
    }

    public function createNewColor(string $color): Color
    {
        $this->color->color = $color;

        if (!$this->color->save()) {
            throw new InternalErrorException('The error on the server, please, try again', 500);
        }

        return $this->color;
    }
}
