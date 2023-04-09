<?php

namespace App\Factories;

use App\Factories\Interfaces\DirectorFactoryInterface;
use App\Models\Director;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class DirectorFactory implements DirectorFactoryInterface
{
    public function __construct(readonly Director $director)
    {
    }

    /**
     * @param string $name
     * @return Director
     * @throws InternalErrorException
     */
    public function createNewDirector(string $name): Director
    {
        $this->director->name = $name;

        if (!$this->director->save()) {
            throw new InternalErrorException('The error on the server, please, try again', 500);
        }

        return $this->director;
    }
}
