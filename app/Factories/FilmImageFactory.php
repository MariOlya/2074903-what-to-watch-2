<?php

declare(strict_types=1);

namespace App\Factories;

use App\Factories\Interfaces\FilmFileFactoryInterface;
use App\Models\File;
use App\Models\FileType;
use App\Services\FileService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FilmImageFactory implements FilmFileFactoryInterface
{
    public function __construct(
        readonly File $file,
        readonly FileService $service
    )
    {
    }

    /**
     * @param string $link
     * @param string $type
     * @return int
     * @throws InternalErrorException
     */
    public function createFromExternalApi(string $link, string $type, string $title): File
    {
        $fileType = FileType::whereType($type)->first();

        if (!$fileType) {
            throw new NotFoundHttpException(
                message: 'This file type is not found',
                code: 404
            );
        }

        $fileExtension = \Illuminate\Support\Facades\File::extension($link);
        $fileName = 'img/'.implode('-', explode(' ', strtolower($title))).'-'.$type.'.'.$fileExtension;

        $this->file->link = $fileName;
        $this->file->file_type_id = $fileType->id;

        if (!$this->file->save()) {
            throw new InternalErrorException('The error on the server, please, try again', 500);
        }

        return $this->file;
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
        $fileType = FileType::whereType($type)->first();

        if (!$fileType) {
            throw new NotFoundHttpException(
                message: 'This file type is not found',
                code: 404
            );
        }

        if (Storage::disk(FileService::PUBLIC_STORAGE)->missing('/images/'.$file)) {
             throw new BadRequestException(
                 'This file does not exist on public storage, please add before',
                 400
             );
        }

        $this->file->link = $link;
        $this->file->file_type_id = $fileType->id;

        if (!$this->file->save()) {
            throw new InternalErrorException('The error on the server, please, try again', 500);
        }

        return $this->file->id;
    }
}
