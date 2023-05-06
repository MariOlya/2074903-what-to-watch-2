<?php

declare(strict_types=1);

namespace App\Factories;

use App\Factories\Interfaces\AvatarFactoryInterface;
use App\Models\File;
use App\Models\FileType;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class AvatarFactory implements AvatarFactoryInterface
{
    public function __construct(
        readonly File $newAvatar
    )
    {
    }

    /**
     * @param string $userName
     * @param string $extension
     * @return File
     * @throws InternalErrorException
     */
    public function createNewAvatar(string $name): File
    {
        $fileName = 'avatars/'.$name;

        $this->newAvatar->link = $fileName;
        $this->newAvatar->file_type_id = FileType::whereType(FileType::AVATAR_TYPE)->value('id');

        if (!$this->newAvatar->save()) {
            throw new InternalErrorException('The error on the server, please, try again', 500);
        }

        return $this->newAvatar;
    }
}
