<?php

declare(strict_types=1);

namespace App\Factories;

use App\Factories\Interfaces\FilmFileFactoryInterface;
use App\Models\File;
use App\Models\FileType;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class FilmImageFactory implements FilmFileFactoryInterface
{
    public function __construct(readonly File $file)
    {
    }

    public function createFromExternalApi(string $link, string $type): int
    {
        // TODO: Implement createFromExternalApi() method.
    }

    /**
     * @param string $link
     * @param string $type
     * @return int
     * @throws InternalErrorException | BadRequestException
     */
    public function createFromEditForm(string $link, string $type): int
    {
        $file = substr($link, 4);

        if (Storage::disk('public')->missing('/images/'.$file)) {
             throw new BadRequestException(
                 'This file does not exist on public storage, please add before',
                 400
             );
        }

        $this->file->link = $link;
        $this->file->file_type_id = FileType::whereType($type)->value('id');

        if (!$this->file->save()) {
            throw new InternalErrorException('The error on the server, please, try again', 500);
        }

        return $this->file->id;
    }
}
