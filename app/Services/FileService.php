<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class FileService
{
    public const FOLDER_IMAGES = 'images';
    public const FOLDER_AVATARS = 'avatars';

    public const PUBLIC_STORAGE = 'public';

    public static function deleteFileFromStorage(
        string $path,
        string $folder = self::FOLDER_IMAGES,
        string $disk = self::PUBLIC_STORAGE
    ): void {
        if (Storage::disk($disk)->missing($folder.$path)) {
            Log::warning('Client tried to delete not existed file '.$folder.$path.' on '.$disk.' storage disk. Need to check');
        }

        Storage::disk($disk)->delete($folder.$path);
    }

    /**
     * @param string $path
     * @param string $title
     * @param string $type
     * @param string $folder
     * @param string $disk
     * @return string
     * @throws InternalErrorException
     */
    public function addFileToStorage(
        string $path,
        string $title,
        string $type,
        string $folder = self::FOLDER_IMAGES,
        string $disk = self::PUBLIC_STORAGE
    ): string {
        $fileUpload = file_get_contents($path);

        if (!$fileUpload) {
            throw new InternalErrorException(
                'We can not read the image, please, try again',
                500
            );
        }

        $fileName = implode('-', explode(' ', strtolower($title))).'-'.$type;

        $path = Storage::disk($disk)->putFileAs(
            $folder, $fileUpload, $fileName
        );

        if (!$path) {
            throw new InternalErrorException(
                'We can not save the image, please, try again',
                500
            );
        }

        return 'img/'.$path;
    }
}
